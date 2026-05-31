<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;
    private string $model;
    private bool $enabled;

    public function __construct()
    {
        $this->apiKey = config('ecws.ai.api_key', '');
        $this->model = config('ecws.ai.model', 'gpt-4o-mini');
        $this->enabled = config('ecws.ai.enabled', false) && !empty($this->apiKey);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function summarize(Transaction $transaction): ?string
    {
        if (!$this->enabled) return null;

        try {
            $content = "الموضوع: {$transaction->subject}\nالمحتوى: {$transaction->content}";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'أنت مساعد ذكي متخصص في تلخيص المعاملات الإدارية باللغة العربية. قم بتلخيص المعاملة في 2-3 سطور فقط.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "لخص هذه المعاملة:\n{$content}"
                    ],
                ],
                'max_tokens' => 200,
                'temperature' => 0.3,
            ]);

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            Log::error('AI Summarize Error: ' . $e->getMessage());
            return null;
        }
    }

    public function suggestResponse(Transaction $transaction): ?string
    {
        if (!$this->enabled) return null;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'أنت مساعد ذكي متخصص في كتابة الردود الرسمية باللغة العربية الفصحى. اكتب رداً رسمياً مناسباً لهذه المعاملة.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "اكتب رداً رسمياً على هذه المعاملة:\nالموضوع: {$transaction->subject}\nالمحتوى: {$transaction->content}"
                    ],
                ],
                'max_tokens' => 500,
                'temperature' => 0.4,
            ]);

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            Log::error('AI Suggest Response Error: ' . $e->getMessage());
            return null;
        }
    }

    public function rewriteLetter(string $content, string $tone = 'formal'): ?string
    {
        if (!$this->enabled) return null;

        try {
            $toneInstruction = match($tone) {
                'formal' => 'بأسلوب رسمي إداري',
                'friendly' => 'بأسلوب ودي مهني',
                'urgent' => 'بأسلوب يوضح الاستعجال',
                default => 'بأسلوب رسمي',
            };

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "أنت مساعد ذكي متخصص في إعادة صياغة الخطابات الإدارية {$toneInstruction} باللغة العربية الفصحى."
                    ],
                    [
                        'role' => 'user',
                        'content' => "أعد صياغة هذا الخطاب:\n{$content}"
                    ],
                ],
                'max_tokens' => 800,
                'temperature' => 0.5,
            ]);

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            Log::error('AI Rewrite Error: ' . $e->getMessage());
            return null;
        }
    }

    public function extractTasks(string $content): ?array
    {
        if (!$this->enabled) return null;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'أنت مساعد ذكي. استخرج قائمة المهام من النص التالي بصيغة JSON array. كل مهمة يجب أن تحتوي على title و priority (high/medium/low).'
                    ],
                    [
                        'role' => 'user',
                        'content' => "استخرج المهام:\n{$content}"
                    ],
                ],
                'max_tokens' => 400,
                'temperature' => 0.3,
                'response_format' => ['type' => 'json_object'],
            ]);

            $result = $response->json('choices.0.message.content');
            $parsed = json_decode($result, true);
            return $parsed['tasks'] ?? [];
        } catch (\Exception $e) {
            Log::error('AI Extract Tasks Error: ' . $e->getMessage());
            return null;
        }
    }

    public function suggestPriority(Transaction $transaction): ?string
    {
        if (!$this->enabled) return null;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'قم بتقييم أولوية المعاملة. رد فقط بـ: urgent, high, normal, أو low.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "الموضوع: {$transaction->subject}\nالمحتوى: {$transaction->content}"
                    ],
                ],
                'max_tokens' => 10,
                'temperature' => 0.2,
            ]);

            return strtolower(trim($response->json('choices.0.message.content')));
        } catch (\Exception $e) {
            Log::error('AI Priority Error: ' . $e->getMessage());
            return null;
        }
    }
}
