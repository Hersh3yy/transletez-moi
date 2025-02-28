<?php

namespace App\Http\Controllers;

use App\Services\OpenAiTranslateService;
use Illuminate\Http\Request;

class TranslateController extends Controller
{
    protected $translateService;

    public function __construct(OpenAiTranslateService $translateService)
    {
        $this->translateService = $translateService;
    }

    public function translate(Request $request, string $target_language)
    {
        $request->validate([
            'json_data' => 'required',
        ]);

        $targetLanguage = strtolower($target_language);

        if (!$this->translateService->isLanguageSupported($targetLanguage)) {
            return response()->json([
                'error' => 'Unsupported target language'
            ], 422);
        }

        // Get json_data input
        $jsonInput = $request->input('json_data');

        // Handle different input formats
        if (is_array($jsonInput)) {
            // Already an array (from API client)
            $jsonData = $jsonInput;
        } else {
            // Try to decode if it's a string (from web form)
            $jsonData = json_decode($jsonInput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'The json data field must be valid JSON.',
                    'errors' => [
                        'json_data' => ['The json data field must be valid JSON.']
                    ]
                ], 422);
            }
        }

        if (empty($jsonData) || !is_array($jsonData)) {
            return response()->json([
                'error' => 'Invalid JSON data provided. Please ensure you are sending a valid JSON object.'
            ], 422);
        }

        try {
            $translatedData = $this->translateService->translateArray($jsonData, $targetLanguage);

            return response()->json([
                'data' => $translatedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Translation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
