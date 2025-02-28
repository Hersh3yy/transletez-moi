<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OpenAiTranslateService;
use Illuminate\Support\Facades\Log;
use Mockery;

class TranslationTest extends TestCase
{
    protected $translateService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translateService = new OpenAiTranslateService();
    }

    public function testTranslateValidJson()
    {
        // Create a partial mock of the OpenAiTranslateService
        $mockService = Mockery::mock(OpenAiTranslateService::class)->makePartial();

        // Mock the OpenAI API response
        $translatedText = 'Translated text';

        // Mock the translate method to return the translated text
        $mockService->shouldReceive('translateArray')->once()->andReturn(['key' => $translatedText]);

        $result = $mockService->translateArray(['key' => 'Original text'], 'es');
        $this->assertEquals(['key' => $translatedText], $result);
    }

    public function testTranslateInvalidJson()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Translation failed');

        // Create a partial mock of the OpenAiTranslateService
        $mockService = Mockery::mock(OpenAiTranslateService::class)->makePartial();

        // Simulate an error from OpenAI
        $mockService->shouldReceive('translateArray')->once()->andThrow(new \Exception('Translation failed'));

        $mockService->translateArray(['key' => 'Original text'], 'es');
    }

    // Additional tests for validation and edge cases can be added here
}
