<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAiTranslateService
{
    protected $maxRetries = 3;
    protected $retryDelay = 1000; // milliseconds

    protected $supportedLanguages = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'it' => 'Italian',
        'nl' => 'Dutch',
        'pt' => 'Portuguese',
    ];

    /**
     * We could validate the language provided with OpenAI as well but for simplicity for now I will give the user limite choices
     */
    public function isLanguageSupported(string $language): bool
    {
        return isset($this->supportedLanguages[$language]) || 
               in_array(strtolower($language), array_map('strtolower', $this->supportedLanguages));
    }

    public function translate(string $text, string $targetLanguage)
    {
        $attempts = 0;
        
        while ($attempts < $this->maxRetries) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are a professional translator. Translate the following text to {$targetLanguage}. Maintain any markdown formatting if present. Only respond with the translation, no explanations."
                        ],
                        ['role' => 'user', 'content' => $text],
                    ],
                ]);

                return $response->choices[0]->message->content;

            } catch (Exception $e) {
                $attempts++;
                Log::warning("OpenAI translation attempt {$attempts} failed: " . $e->getMessage());

                if ($attempts === $this->maxRetries) {
                    throw new Exception("Translation failed after {$this->maxRetries} attempts: " . $e->getMessage());
                }

                usleep($this->retryDelay * $attempts); // Exponential backoff
            }
        }
    }

    public function translateArray(array $data, string $targetLanguage): array
    {
        $translatedData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $translatedData[$key] = $this->translateArray($value, $targetLanguage);
            } else {
                $translatedData[$key] = $this->translate($value, $targetLanguage);
            }
        }

        return $translatedData;
    }
} 