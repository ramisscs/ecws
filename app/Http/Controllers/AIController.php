<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AIController extends Controller
{
    public function __construct(
        private AIService $aiService
    ) {}

    public function summarize(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        if (!$this->aiService->isEnabled()) {
            return response()->json(['error' => 'AI غير مفعل'], 400);
        }

        $summary = $this->aiService->summarize($transaction);

        return response()->json(['summary' => $summary]);
    }

    public function suggestResponse(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        if (!$this->aiService->isEnabled()) {
            return response()->json(['error' => 'AI غير مفعل'], 400);
        }

        $response = $this->aiService->suggestResponse($transaction);

        return response()->json(['response' => $response]);
    }

    public function rewrite(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'tone' => 'nullable|string|in:formal,friendly,urgent',
        ]);

        if (!$this->aiService->isEnabled()) {
            return response()->json(['error' => 'AI غير مفعل'], 400);
        }

        $rewritten = $this->aiService->rewriteLetter(
            $request->input('content'),
            $request->input('tone', 'formal')
        );

        return response()->json(['content' => $rewritten]);
    }

    public function extractTasks(Request $request)
    {
        $request->validate(['content' => 'required|string|max:5000']);

        if (!$this->aiService->isEnabled()) {
            return response()->json(['error' => 'AI غير مفعل'], 400);
        }

        $tasks = $this->aiService->extractTasks($request->input('content'));

        return response()->json(['tasks' => $tasks]);
    }

    public function suggestPriority(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        if (!$this->aiService->isEnabled()) {
            return response()->json(['error' => 'AI غير مفعل'], 400);
        }

        $priority = $this->aiService->suggestPriority($transaction);

        return response()->json(['priority' => $priority]);
    }
}
