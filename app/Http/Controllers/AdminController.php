<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Template;
use App\Models\AuditLog;
use App\Enums\UserRole;
use App\Repositories\UserRepository;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private AuditService $auditService,
    ) {}

    // ============= إدارة المستخدمين =============
    public function users(Request $request)
    {
        $filters = $request->only(['name', 'employee_id', 'role', 'department_id', 'is_active', 'per_page']);
        $users = $this->userRepository->search($filters);
        $departments = Department::where('is_active', true)->get();
        $roles = UserRole::cases();

        return view('admin.users', compact('users', 'departments', 'roles'));
    }

    // إنشاء مستخدم جديد (مدير النظام فقط)
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|max:20|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'civil_id' => 'nullable|string|max:20|unique:users',
            'job_title' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:' . implode(',', array_column(UserRole::cases(), 'value')),
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'boolean',
        ], [
            'employee_id.required' => 'الرقم الوظيفي مطلوب',
            'employee_id.unique' => 'الرقم الوظيفي مستخدم مسبقاً',
            'name.required' => 'الاسم مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'civil_id.unique' => 'الرقم المدني مستخدم مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'role.required' => 'الدور الوظيفي مطلوب',
            'department_id.required' => 'القسم مطلوب',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);
        $data['password_changed'] = false; // يجب تغييرها عند أول دخول
        $data['is_active'] = $request->boolean('is_active', true);

        $user = $this->userRepository->create($data);

        // تسجيل في Audit Log
        $this->auditService->log(
            auth()->id(),
            'create_user',
            User::class,
            $user->id,
            null,
            $data,
            'إنشاء مستخدم جديد: ' . $user->name . ' (رقم وظيفي: ' . $user->employee_id . ')'
        );

        return redirect()->back()->with('success', 
            'تم إنشاء المستخدم بنجاح! ' . $user->name . ' — الرقم الوظيفي: ' . $user->employee_id);
    }

    // تحديث بيانات مستخدم
    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'civil_id' => 'nullable|string|max:20|unique:users,civil_id,' . $user->id,
            'job_title' => 'nullable|string|max:255',
            'role' => 'required|string|in:' . implode(',', array_column(UserRole::cases(), 'value')),
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $oldData = $user->toArray();
        $this->userRepository->update($user->id, $validator->validated());
        $user->refresh();

        $this->auditService->log(
            auth()->id(),
            'update_user',
            User::class,
            $user->id,
            $oldData,
            $user->toArray(),
            'تحديث بيانات المستخدم: ' . $user->name
        );

        return redirect()->back()->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    // تفعيل/تعطيل مستخدم
    public function toggleUser(User $user)
    {
        // منع تعطيل المدير العام
        if ($user->isSuperAdmin() && $user->is_active) {
            return redirect()->back()->with('error', 'لا يمكن تعطيل حساب المدير العام');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'تفعيل' : 'تعطيل';

        $this->auditService->log(
            auth()->id(),
            $user->is_active ? 'activate_user' : 'deactivate_user',
            User::class,
            $user->id,
            null,
            null,
            $status . ' المستخدم: ' . $user->name
        );

        return redirect()->back()->with('success', "تم {$status} المستخدم بنجاح");
    }

    // إعادة تعيين كلمة المرور
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => Hash::make($request->input('new_password')),
            'password_changed' => false, // يجب تغييرها عند الدخول
        ]);

        $this->auditService->log(
            auth()->id(),
            'reset_password',
            User::class,
            $user->id,
            null,
            null,
            'إعادة تعيين كلمة المرور للمستخدم: ' . $user->name
        );

        return redirect()->back()->with('success', 
            'تم إعادة تعيين كلمة المرور للمستخدم ' . $user->name . '. كلمة المرور الجديدة: ' . $request->input('new_password'));
    }

    // ============= الأقسام =============
    public function departments()
    {
        $departments = Department::withCount('users')->paginate(20);
        return view('admin.departments', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments',
            'description' => 'nullable|string',
        ]);

        Department::create($request->all());

        return redirect()->back()->with('success', 'تم إنشاء القسم بنجاح');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث القسم بنجاح');
    }

    // ============= سجل التدقيق =============
    public function auditLogs(Request $request)
    {
        $filters = $request->only(['user_id', 'action', 'entity_type', 'date_from', 'date_to']);
        $logs = $this->auditService->getLogs($filters);
        $stats = $this->auditService->getStatistics();

        return view('admin.audit', compact('logs', 'stats'));
    }

    // ============= الإعدادات =============
    public function settings()
    {
        $settings = [
            'app_name' => config('app.name'),
            'prefix' => config('ecws.prefix', 'SSCS'),
            'ai_enabled' => config('ecws.ai.enabled', false),
            'ai_model' => config('ecws.ai.model', 'gpt-4o-mini'),
            'max_file_size' => config('ecws.files.max_size', 10240),
            'allowed_extensions' => config('ecws.files.extensions', 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip'),
            'audit_retention' => config('ecws.audit.retention', 365),
            'whatsapp_enabled' => config('ecws.whatsapp.enabled', false),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'prefix' => 'required|string|max:20',
            'ai_enabled' => 'boolean',
            'max_file_size' => 'required|integer|min:1024',
            'audit_retention' => 'required|integer|min:30',
        ]);

        return redirect()->back()->with('success', 'تم تحديث الإعدادات');
    }

    // ============= القوالب =============
    public function templates()
    {
        $templates = Template::with('creator')->paginate(20);
        $types = \App\Enums\TransactionType::cases();
        return view('admin.templates', compact('templates', 'types'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_column(\App\Enums\TransactionType::cases(), 'value')),
            'subject' => 'nullable|string|max:500',
            'content' => 'required|string',
        ]);

        Template::create([
            ...$request->all(),
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تم إنشاء القالب بنجاح');
    }

    public function updateTemplate(Request $request, Template $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'subject' => 'nullable|string',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $template->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث القالب بنجاح');
    }

    // ============= التقارير =============
    public function reports(Request $request)
    {
        $period = $request->input('period', 'month');
        $departmentId = $request->input('department_id');

        $reportData = $this->generateReport($period, $departmentId);

        return view('admin.reports', compact('reportData', 'period'));
    }

    public function backup()
    {
        return view('admin.backup');
    }

    private function generateReport(string $period, ?int $departmentId): array
    {
        $dateRange = match($period) {
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $query = \App\Models\Transaction::whereBetween('created_at', $dateRange);

        if ($departmentId) {
            $query->byDepartment($departmentId);
        }

        return [
            'total' => (clone $query)->count(),
            'by_type' => (clone $query)->selectRaw('type, count(*) as count')
                ->groupBy('type')->pluck('count', 'type')->toArray(),
            'by_status' => (clone $query)->selectRaw('status, count(*) as count')
                ->groupBy('status')->pluck('count', 'status')->toArray(),
            'by_department' => (clone $query)->selectRaw('from_department_id, count(*) as count')
                ->groupBy('from_department_id')->pluck('count', 'from_department_id')->toArray(),
            'avg_processing_time' => (clone $query)->whereNotNull('closed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
                ->value('avg_hours'),
        ];
    }
}
