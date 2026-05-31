<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Services\WorkflowService;
use App\Repositories\TransactionRepository;
use App\Models\Transaction;
use App\Models\Department;
use App\Models\Template;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private WorkflowService $workflowService,
        private TransactionRepository $repository,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only([
            'tracking_number', 'type', 'status', 'subject',
            'department_id', 'date_from', 'date_to', 'sort_by', 'sort_order', 'per_page'
        ]);

        $transactions = $this->repository->search($filters);
        $types = TransactionType::cases();
        $statuses = TransactionStatus::cases();
        $departments = Department::where('is_active', true)->get();

        return view('transactions.index', compact('transactions', 'types', 'statuses', 'departments'));
    }

    public function create()
    {
        $types = TransactionType::cases();
        $departments = Department::where('is_active', true)->get();
        $templates = Template::where('is_active', true)->get();

        return view('transactions.create', compact('types', 'departments', 'templates'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:' . implode(',', array_column(TransactionType::cases(), 'value')),
            'subject' => 'required|string|max:500',
            'content' => 'required|string',
            'to_department_id' => 'required|exists:departments,id',
            'to_user_id' => 'nullable|exists:users,id',
            'is_secret' => 'boolean',
            'is_urgent' => 'boolean',
            'due_date' => 'nullable|date',
            'template_id' => 'nullable|exists:templates,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $transaction = $this->transactionService->create(
            $validator->validated(),
            Auth::id()
        );

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->storeAttachment($transaction, $file);
            }
        }

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'تم إنشاء المعاملة بنجاح: ' . $transaction->tracking_number);
    }

    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        if ($transaction->is_secret && !$user->canViewSecrets()) {
            abort(403);
        }

        $transaction->load([
            'fromDepartment', 'toDepartment',
            'fromUser', 'toUser',
            'attachments', 'workflowLogs.user',
            'children', 'parent'
        ]);

        $history = $this->workflowService->getTransactionHistory($transaction);

        return view('transactions.show', compact('transaction', 'history'));
    }

    public function transfer(Request $request, Transaction $transaction)
    {
        $validator = Validator::make($request->all(), [
            'to_department_id' => 'required|exists:departments,id',
            'to_user_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $this->transactionService->transfer(
            $transaction->id,
            $validator->validated(),
            Auth::id()
        );

        return redirect()->back()->with('success', 'تم التحويل بنجاح');
    }

    public function approve(Request $request, Transaction $transaction)
    {
        $this->transactionService->approve(
            $transaction->id,
            Auth::id(),
            $request->input('notes')
        );

        return redirect()->back()->with('success', 'تم الاعتماد بنجاح');
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        $this->transactionService->reject(
            $transaction->id,
            Auth::id(),
            $request->input('reason')
        );

        return redirect()->back()->with('success', 'تم الرفض بنجاح');
    }

    public function close(Request $request, Transaction $transaction)
    {
        $this->transactionService->close(
            $transaction->id,
            Auth::id(),
            $request->input('notes')
        );

        return redirect()->back()->with('success', 'تم الإغلاق بنجاح');
    }

    public function track(Request $request)
    {
        $trackingNumber = $request->input('tracking_number');
        $transaction = null;

        if ($trackingNumber) {
            $transaction = Transaction::with([
                'fromDepartment', 'toDepartment',
                'workflowLogs.user'
            ])->where('tracking_number', $trackingNumber)->first();
        }

        return view('transactions.track', compact('transaction', 'trackingNumber'));
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['fromDepartment', 'toDepartment', 'fromUser', 'toUser', 'workflowLogs.user']);
        return view('transactions.print', compact('transaction'));
    }

    private function storeAttachment(Transaction $transaction, $file): void
    {
        $path = $file->store('attachments/' . $transaction->id, 'local');

        $transaction->attachments()->create([
            'user_id' => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'file_name' => basename($path),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'is_encrypted' => $transaction->is_secret,
            'checksum' => hash_file('sha256', $file->getRealPath()),
        ]);
    }
}
