<?php

namespace App\Services;

class ProposalAnalysisService
{
    private $apiKeys = [];

    public function __construct()
    {
        $this->apiKeys = array_filter([
            getenv('DEEPSEEK_API_KEY'),
            getenv('GROQ_API_KEY'),
            getenv('OPENAI_API_KEY'),
            getenv('GEMINI_API_KEY_1'),
            getenv('GEMINI_API_KEY_2'),
            getenv('GEMINI_API_KEY_3'),
            getenv('GEMINI_API_KEY_4'),
            getenv('GEMINI_API_KEY_5'),
        ]);

        if (empty($this->apiKeys) && ENVIRONMENT !== 'production') {
            $this->apiKeys = [];
        }
    }

    public function analyze($filePath, $division = '')
    {
        set_time_limit(120);

        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'File not found'];
        }

        $fullText = $this->extractPdfText($filePath);
        if (empty($fullText) || strlen($fullText) < 50) {
            return ['success' => false, 'message' => 'Tidak dapat mengekstrak teks dari proposal.'];
        }

        $llmResult = $this->callLLM($fullText, $division);

        if (!$llmResult || isset($llmResult['error'])) {
             return ['success' => false, 'message' => 'Gagal memproses proposal menggunakan AI: ' . ($llmResult['error'] ?? 'Unknown error')];
        }
        
        return [
            'success' => true,
            'data' => $llmResult
        ];
    }

    private function extractPdfText($filePath)
    {
        $mimeType = mime_content_type($filePath);
        if (strpos($mimeType, 'pdf') !== false) {
            if (class_exists('Smalot\PdfParser\Parser')) {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($filePath);
                    $text = $pdf->getText();
                    if (!empty($text)) return substr($text, 0, 15000);
                } catch (\Throwable $e) {
                    error_log("Smalot Parser Failed: " . $e->getMessage());
                }
            }

            $cmd = "pdftotext -layout " . escapeshellarg($filePath) . " -";
            $text = shell_exec($cmd);
            if ($text) return substr($text, 0, 15000); 
        }
        return '';
    }

    private function callLLM($text, $division)
    {
        foreach ($this->apiKeys as $key) {
             $isOpenAI = strpos($key, 'sk-proj-') === 0;
             $isDeepSeek = !$isOpenAI && strpos($key, 'sk-') === 0;
             $isGroq = strpos($key, 'gsk_') === 0;

             $prompt = "You are an Internship Proposal Reviewer. Analyze the following proposal text and return ONLY JSON with keys: 'summary' (brief Indonesian summary), 'relevance' (integer 0-100 how relevant it is to the chosen division), 'strengths' (array of strings in Indonesian), 'weaknesses' (array of strings in Indonesian), 'verdict' (string: Direkomendasikan or Tidak Direkomendasikan). The candidate applied for division: '{$division}'. Text:\n\n" . $text;

             if ($isOpenAI) {
                 $url = "https://api.openai.com/v1/chat/completions";
                 $data = [
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'response_format' => ['type' => 'json_object']
                 ];
                 $headers = ['Authorization: Bearer ' . $key, 'Content-Type: application/json'];
             } elseif ($isDeepSeek) {
                 $url = "https://api.deepseek.com/chat/completions";
                 $data = [
                    'model' => 'deepseek-chat',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'response_format' => ['type' => 'json_object']
                 ];
                 $headers = ['Authorization: Bearer ' . $key, 'Content-Type: application/json'];
             } elseif ($isGroq) {
                 $url = "https://api.groq.com/openai/v1/chat/completions";
                 $data = [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'response_format' => ['type' => 'json_object']
                 ];
                 $headers = ['Authorization: Bearer ' . $key, 'Content-Type: application/json'];
             } else {
                 $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$key}";
                 $geminiPrompt = "Return JSON with keys 'summary', 'relevance' (int), 'strengths' (array), 'weaknesses' (array), and 'verdict'. Analyze this internship proposal for division {$division}: " . $text;
                 $data = [
                    'contents' => [['parts' => [['text' => $geminiPrompt]]]],
                    'generationConfig' => ['response_mime_type' => 'application/json']
                 ];
                 $headers = ['Content-Type: application/json'];
             }

             $ch = curl_init($url);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_POST, true);
             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
             curl_setopt($ch, CURLOPT_TIMEOUT, 30);
             
             $response = curl_exec($ch);
             $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
             curl_close($ch);

             if ($httpCode === 200) {
                  $json = json_decode($response, true);
                  if ($isGroq || $isDeepSeek || $isOpenAI) {
                      $content = $json['choices'][0]['message']['content'] ?? '{}';
                  } else {
                      $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                  }
                  $cleanJson = str_replace(['```json', '```'], '', $content);
                  return json_decode($cleanJson, true);
             }
             continue;
        }
        return ['error' => 'All keys failed'];
    }
}
