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
        $mockResponse = (object)[
            'choices' => [(object)['message' => (object)['content' => 'Translated text']]]
        ];

        // Mock the translate method to return the mock response
        $mockService->shouldReceive('translate')->once()->andReturn($mockResponse);

        $result = $mockService->translate('Original text', 'es');
        $this->assertEquals('Translated text', $result);
    }

    public function testTranslateInvalidJson()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Translation failed');

        // Create a partial mock of the OpenAiTranslateService
        $mockService = Mockery::mock(OpenAiTranslateService::class)->makePartial();

        // Simulate an error from OpenAI
        $mockService->shouldReceive('translate')->once()->andThrow(new \Exception('API error'));

        $mockService->translate('Original text', 'es');
    }

    // Additional tests for validation and edge cases can be added here
}
