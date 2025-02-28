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
            'json_data' => 'nullable|string',
            'file' => 'nullable|file|mimes:json',
        ]);

        $targetLanguage = strtolower($target_language);

        if (!$this->translateService->isLanguageSupported($targetLanguage)) {
            return response()->json([
                'error' => 'Unsupported target language'
            ], 422);
        }

        if ($request->hasFile('file')) {
            $jsonData = json_decode(file_get_contents($request->file('file')->getRealPath()), true);
        } else {
            $jsonData = $request->input('json_data', null);
            if ($jsonData) {
                $jsonData = json_decode($jsonData, true);
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
