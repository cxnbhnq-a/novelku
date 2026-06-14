<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityLogService
{
    /**
     * Log activity ke database
     */
    public static function log(
        string $logType,
        string $message,
        string $status = 'success',
        string $severity = 'info',
        ?string $email = null,
        ?string $userId = null,
        ?array $payload = null,
        ?array $response = null,
        ?string $stackTrace = null,
        Request $request = null
    ) {
        try {
            $request = $request ?? request();

            ActivityLog::create([
                'log_type' => $logType,
                'message' => $message,
                'email' => $email ?? auth()->user()?->email ?? null,
                'user_id' => $userId ?? auth()->user()?->id ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->path(),
                'payload' => $payload ? json_encode($payload) : null,
                'response' => $response ? json_encode($response) : null,
                'stack_trace' => $stackTrace,
                'status' => $status,
                'severity' => $severity,
                'created_at' => now('Asia/Jakarta'),
            ]);
        } catch (\Exception $e) {
            // Fallback ke file log jika database gagal
            Log::error('ActivityLogService Error: ' . $e->getMessage());
        }
    }

    /**
     * Log login failure
     */
    public static function logLoginFailed($email, $reason = 'Invalid credentials', Request $request = null)
    {
        // Check untuk brute force attempt
        $recentAttempts = ActivityLog::where('email', $email)
            ->where('log_type', 'login_failed')
            ->where('created_at', '>', now()->subMinutes(15))
            ->count();

        $severity = $recentAttempts >= 5 ? 'critical' : ($recentAttempts >= 3 ? 'warning' : 'info');

        // Log suspicious activity jika terlalu banyak attempt
        if ($recentAttempts >= 5) {
            self::log(
                'suspicious_activity',
                "Banyak percobaan login gagal dari email: {$email}. Jumlah percobaan dalam 15 menit terakhir: " . ($recentAttempts + 1),
                'failed',
                'critical',
                $email,
                null,
                null,
                null,
                null,
                $request
            );
        }

        self::log(
            'login_failed',
            "Login gagal untuk email: {$email}. Alasan: {$reason}",
            'failed',
            $severity,
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log login success
     */
    public static function logLoginSuccess($user, Request $request = null)
    {
        self::log(
            'login_success',
            "User berhasil login: {$user->email}",
            'success',
            'info',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log register failure
     */
    public static function logRegisterFailed($email, $reason = 'Validation failed', Request $request = null)
    {
        self::log(
            'register_failed',
            "Registrasi gagal untuk email: {$email}. Alasan: {$reason}",
            'failed',
            'warning',
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log register success
     */
    public static function logRegisterSuccess($user, Request $request = null)
    {
        self::log(
            'register_success',
            "User baru berhasil terdaftar: {$user->email}",
            'success',
            'info',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log OTP failure
     */
    public static function logOtpFailed($email, $reason = 'Invalid OTP', Request $request = null)
    {
        // Check multiple OTP failures
        $recentAttempts = ActivityLog::where('email', $email)
            ->where('log_type', 'otp_failed')
            ->where('created_at', '>', now()->subMinutes(10))
            ->count();

        if ($recentAttempts >= 3) {
            self::log(
                'suspicious_activity',
                "Banyak percobaan OTP gagal dari email: {$email}. Jumlah: " . ($recentAttempts + 1),
                'failed',
                'critical',
                $email,
                null,
                null,
                null,
                null,
                $request
            );
        }

        self::log(
            'otp_failed',
            "OTP gagal untuk email: {$email}. Alasan: {$reason}",
            'failed',
            $recentAttempts >= 3 ? 'critical' : 'warning',
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log OTP success
     */
    public static function logOtpSuccess($email, Request $request = null)
    {
        self::log(
            'otp_success',
            "OTP berhasil diverifikasi untuk email: {$email}",
            'success',
            'info',
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log email verification failure
     */
    public static function logEmailVerificationFailed($email, $reason = 'Verification failed', Request $request = null)
    {
        self::log(
            'email_verification_failed',
            "Verifikasi email gagal untuk: {$email}. Alasan: {$reason}",
            'failed',
            'warning',
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log email verification success
     */
    public static function logEmailVerificationSuccess($user, Request $request = null)
    {
        self::log(
            'email_verification_success',
            "Email berhasil diverifikasi untuk: {$user->email}",
            'success',
            'info',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log password reset failure
     */
    public static function logPasswordResetFailed($email, $reason = 'Reset failed', Request $request = null)
    {
        self::log(
            'password_reset_failed',
            "Reset password gagal untuk: {$email}. Alasan: {$reason}",
            'failed',
            'warning',
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log password reset success
     */
    public static function logPasswordResetSuccess($email, Request $request = null)
    {
        self::log(
            'password_reset_success',
            "Password berhasil direset untuk: {$email}",
            'success',
            'info',
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log profile update failure
     */
    public static function logProfileUpdateFailed($user, $reason = 'Update failed', Request $request = null)
    {
        self::log(
            'profile_update_failed',
            "Update profil gagal untuk: {$user->email}. Alasan: {$reason}",
            'failed',
            'warning',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log profile update success
     */
    public static function logProfileUpdateSuccess($user, $changes = null, Request $request = null)
    {
        $message = "Profil berhasil diperbarui untuk: {$user->email}";
        if ($changes) {
            $message .= ". Perubahan: " . implode(', ', array_keys($changes));
        }

        self::log(
            'profile_update_success',
            $message,
            'success',
            'info',
            $user->email,
            $user->id,
            null,
            $changes,
            null,
            $request
        );
    }

    /**
     * Log file upload failure
     */
    public static function logFileUploadFailed($user, $reason = 'Upload failed', Request $request = null)
    {
        self::log(
            'file_upload_failed',
            "Upload file gagal untuk: {$user->email}. Alasan: {$reason}",
            'failed',
            'warning',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log file upload success
     */
    public static function logFileUploadSuccess($user, $fileName = null, Request $request = null)
    {
        $message = "File berhasil diunggah oleh: {$user->email}";
        if ($fileName) {
            $message .= ". File: {$fileName}";
        }

        self::log(
            'file_upload_success',
            $message,
            'success',
            'info',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log payment/transaction failure
     */
    public static function logPaymentFailed($user, $reason = 'Payment failed', Request $request = null)
    {
        self::log(
            'payment_failed',
            "Transaksi gagal untuk: {$user->email}. Alasan: {$reason}",
            'failed',
            'warning',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log payment/transaction success
     */
    public static function logPaymentSuccess($user, $transactionId = null, Request $request = null)
    {
        $message = "Transaksi berhasil untuk: {$user->email}";
        if ($transactionId) {
            $message .= ". ID: {$transactionId}";
        }

        self::log(
            'payment_success',
            $message,
            'success',
            'info',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log logout
     */
    public static function logLogout($user, Request $request = null)
    {
        self::log(
            'logout',
            "User logout: {$user->email}",
            'success',
            'info',
            $user->email,
            $user->id,
            null,
            null,
            null,
            $request
        );
    }

    /**
     * Log suspicious activity
     */
    public static function logSuspiciousActivity($message, $email = null, Request $request = null)
    {
        self::log(
            'suspicious_activity',
            $message,
            'failed',
            'critical',
            $email,
            null,
            null,
            null,
            null,
            $request
        );
    }
}
