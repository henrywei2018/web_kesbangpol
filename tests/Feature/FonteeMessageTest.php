<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\FonteService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FonteeMessageTest extends TestCase
{
    /**
     * Test basic message sending functionality
     */
    public function test_can_send_simple_message()
    {
        // Mock the HTTP response from Fonnte API
        Http::fake([
            'api.fonnte.com/send' => Http::response([
                'status' => true,
                'id' => 'msg_123456',
                'reason' => 'success'
            ], 200)
        ]);

        $fonteService = new FonteService();
        
        $result = $fonteService->sendMessage(
            target: '085387555568',
            message: 'Hello, this is a test message!'
        );

        // Assert the message was sent successfully
        $this->assertTrue($result['success']);
        $this->assertEquals('msg_123456', $result['message_id']);
        $this->assertTrue($result['status']);
        
        // Verify HTTP request was made with correct parameters
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send' &&
                   $request['target'] === '6285387555568' && // Should be formatted to international
                   $request['message'] === 'Hello, this is a test message!' &&
                   $request['countryCode'] === '62';
        });
    }

    /**
     * Test phone number formatting
     */
    public function test_formats_phone_number_correctly()
    {
        Http::fake([
            'api.fonnte.com/send' => Http::response(['status' => true], 200)
        ]);

        $fonteService = new FonteService();

        // Test various phone number formats
        $testCases = [
            '085387555568' => '6285387555568',
            '85387555568' => '6285387555568',
            '6285387555568' => '6285387555568',
        ];

        foreach ($testCases as $input => $expected) {
            $fonteService->sendMessage($input, 'Test message');
            
            Http::assertSent(function ($request) use ($expected) {
                return $request['target'] === $expected;
            });
        }
    }

    /**
     * Test error handling
     */
    public function test_handles_api_errors_gracefully()
    {
        // Mock API error response
        Http::fake([
            'api.fonnte.com/send' => Http::response([
                'status' => false,
                'reason' => 'Invalid phone number'
            ], 400)
        ]);

        $fonteService = new FonteService();
        
        $result = $fonteService->sendMessage(
            target: 'invalid_phone',
            message: 'Test message'
        );

        // Assert error is handled properly
        $this->assertFalse($result['success']);
        $this->assertNull($result['message_id']);
        $this->assertEquals('Invalid phone number format', $result['error']);
    }

    /**
     * Manual test - uncomment to test with real API
     * Make sure you have FONNTE_TOKEN in your .env file
     */
    public function test_real_api_call()
    {
        $fonteService = new FonteService();
        
        $result = $fonteService->sendMessage(
            target: '085387555568', // Your test number
            message: 'Hello from Laravel test! 🚀'
        );
    
        dump($result); // See the actual response
        
        $this->assertTrue($result['success']);
    }
}

// Quick Artisan Command Test (optional)
// You can also create a simple artisan command for manual testing:

/*
// File: app/Console/Commands/TestFonteeMessage.php

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FonteService;

class TestFonteeMessage extends Command
{
    protected $signature = 'fontee:test {phone} {message}';
    protected $description = 'Test sending WhatsApp message via Fontee';

    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message');

        $fonteService = app(FonteService::class);
        $result = $fonteService->sendMessage($phone, $message);

        if ($result['success']) {
            $this->info("✅ Message sent successfully!");
            $this->line("Message ID: " . $result['message_id']);
        } else {
            $this->error("❌ Failed to send message");
            $this->line("Error: " . ($result['error'] ?? $result['reason'] ?? 'Unknown error'));
        }

        return $result['success'] ? 0 : 1;
    }
}

// Register in app/Console/Kernel.php:
// protected $commands = [
//     Commands\TestFonteeMessage::class,
// ];

// Then run: php artisan fontee:test 08123456789 "Hello World!"
*/