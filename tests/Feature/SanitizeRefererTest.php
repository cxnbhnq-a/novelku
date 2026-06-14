<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SanitizeRefererTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/_test/referer-back', function () {
            return redirect()->back();
        });
    }

    public function test_external_referer_cannot_control_back_redirect(): void
    {
        $response = $this->withHeader('Referer', 'https://evil.com/phishing')
            ->get('/_test/referer-back');

        $response->assertRedirect('/');
    }

    public function test_same_origin_referer_still_controls_back_redirect(): void
    {
        $response = $this->withHeader('Referer', 'https://192.168.56.240/source')
            ->get('/_test/referer-back');

        $response->assertRedirect('https://192.168.56.240/source');
    }
}
