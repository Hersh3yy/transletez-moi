<?php

namespace Tests\Unit;

use App\Services\OpenAiTranslateService;
use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use App\Http\Controllers\TranslateController;

class TranslationServiceTest extends TestCase
{
    /**
     * Test language support checker.
     */
    public function test_language_support_detector(): void
    {
        $service = new OpenAiTranslateService();

        // Test supported languages
        $this->assertTrue($service->isLanguageSupported('en'));
        $this->assertTrue($service->isLanguageSupported('es'));
        $this->assertTrue($service->isLanguageSupported('fr'));

        // Test case insensitivity
        $this->assertTrue($service->isLanguageSupported('EN'));
        $this->assertTrue($service->isLanguageSupported('Es'));

        // Test unsupported languages
        $this->assertFalse($service->isLanguageSupported('jp'));
        $this->assertFalse($service->isLanguageSupported('ru'));
        $this->assertFalse($service->isLanguageSupported('invalid'));
    }

    /**
     * Test array translation functionality with mocked service.
     */
    public function test_translate_array_calls_translate_for_each_item(): void
    {
        // Create a partial mock of the service
        $service = Mockery::mock(OpenAiTranslateService::class)->makePartial();

        // Set up the expectation for the translate method
        $service->shouldReceive('translate')
            ->with('Hello', 'es')
            ->once()
            ->andReturn('Hola');

        $service->shouldReceive('translate')
            ->with('World', 'es')
            ->once()
            ->andReturn('Mundo');

        // Test with a simple array
        $input = ['greeting' => 'Hello', 'subject' => 'World'];
        $expected = ['greeting' => 'Hola', 'subject' => 'Mundo'];

        $result = $service->translateArray($input, 'es');

        $this->assertEquals($expected, $result);
    }

    /**
     * Test controller validation of JSON data.
     */
    public function test_controller_validates_json_input(): void
    {
        // Mock the translation service
        $serviceMock = Mockery::mock(OpenAiTranslateService::class);
        $serviceMock->shouldReceive('isLanguageSupported')
            ->with('es')
            ->andReturn(true);

        // Create a controller with our mocked service
        $controller = new TranslateController($serviceMock);

        // Set up JSON data expectations
        $jsonData = '{"greeting":"Hello"}';
        $decodedJson = ['greeting' => 'Hello'];

        $serviceMock->shouldReceive('translateArray')
            ->with($decodedJson, 'es')
            ->andReturn(['greeting' => 'Hola']);

        // Create a request with JSON data
        $request = new Request();
        $request->merge(['json_data' => $jsonData]);

        // Call the controller method
        $response = $controller->translate($request, 'es');

        // Assert the response contains the expected data
        $this->assertEquals(
            ['data' => ['greeting' => 'Hola']],
            $response->getData(true)
        );
    }

    /**
     * Test controller handling of invalid JSON.
     */
    public function test_controller_rejects_invalid_json(): void
    {
        // Mock the translation service
        $serviceMock = Mockery::mock(OpenAiTranslateService::class);
        $serviceMock->shouldReceive('isLanguageSupported')
            ->with('es')
            ->andReturn(true);

        // Create a controller with our mocked service
        $controller = new TranslateController($serviceMock);

        // Create a request with invalid JSON data
        $request = new Request();
        $request->merge(['json_data' => 'not valid json']);

        // Call the controller method
        $response = $controller->translate($request, 'es');

        // Assert the response indicates an error
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
