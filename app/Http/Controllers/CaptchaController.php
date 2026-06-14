<?php

namespace App\Http\Controllers;

use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;

class CaptchaController extends Controller
{
    public function refresh(): JsonResponse
    {
        $question = CaptchaService::generate();

        $html = view('auth.partials.captcha', [
            'captchaQuestion' => $question,
        ])->render();

        return response()->json(['html' => $html]);
    }
}
