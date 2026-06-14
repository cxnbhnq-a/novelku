<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminLogsController;
use App\Http\Middleware\SecurityHeaders;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    public function test_untrusted_host_is_rejected(): void
    {
        $this->withHeader('Host', 'evil.example')
            ->get('/')
            ->assertStatus(400);
    }

    public function test_user_serialization_does_not_expose_otp_fields(): void
    {
        $user = new User([
            'otp_code' => 'secret-hash',
            'otp_expires_at' => now(),
        ]);

        $this->assertArrayNotHasKey('otp_code', $user->toArray());
        $this->assertArrayNotHasKey('otp_expires_at', $user->toArray());
    }

    public function test_security_headers_are_added_to_https_responses(): void
    {
        $request = Request::create('https://192.168.56.240/');
        $response = (new SecurityHeaders())->handle($request, fn () => new Response('ok'));

        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertSame('max-age=31536000', $response->headers->get('Strict-Transport-Security'));
    }

    public function test_admin_log_view_escapes_values_before_inner_html_insertion(): void
    {
        $view = file_get_contents(resource_path('views/dashboard/admin-logs.blade.php'));

        $this->assertStringContainsString('function escapeHtml(value)', $view);
        $this->assertStringContainsString('${escapeHtml(data.user_agent)}', $view);
        $this->assertStringNotContainsString('${data.user_agent ||', $view);
    }

    public function test_csv_formula_prefixes_are_neutralized(): void
    {
        $method = new \ReflectionMethod(AdminLogsController::class, 'sanitizeCsvCell');
        $controller = new AdminLogsController();

        $this->assertSame("'=HYPERLINK(\"https://evil.example\")", $method->invoke($controller, '=HYPERLINK("https://evil.example")'));
        $this->assertSame('normal value', $method->invoke($controller, 'normal value'));
    }
}
