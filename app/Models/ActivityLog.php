<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'activity_logs';

    protected $fillable = [
        'log_type', 'message', 'email', 'user_id', 'ip_address',
        'user_agent', 'endpoint', 'payload', 'response',
        'stack_trace', 'status', 'severity', 'attempt_count'
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'created_at' => 'datetime',
    ];

    public static function getLogColor($logType)
    {
        $colors = [
            'login_failed' => 'danger', 'login_success' => 'success',
            'register_failed' => 'warning', 'register_success' => 'success',
            'logout' => 'info', 'otp_failed' => 'warning', 'otp_success' => 'success',
            'profile_update_failed' => 'warning', 'profile_update_success' => 'success',
            'email_verification_failed' => 'warning', 'email_verification_success' => 'success',
            'password_reset_failed' => 'danger', 'password_reset_success' => 'success',
            'file_upload_failed' => 'danger', 'file_upload_success' => 'success',
            'payment_failed' => 'danger', 'payment_success' => 'success',
            'user_deleted_by_admin' => 'warning', 'novel_deleted_by_admin' => 'warning',
            'admin_action' => 'info', 'admin_logs_viewed' => 'info',
            'suspicious_activity' => 'danger',
        ];
        return $colors[$logType] ?? 'secondary';
    }

    // TAMBAHAN: Biar di tabel ada ikonnya
    public static function getLogIcon($logType)
    {
        $icons = [
            'login_failed' => '❌', 'login_success' => '✅',
            'register_failed' => '⚠️', 'register_success' => '🎉',
            'logout' => '🚪', 'otp_failed' => '📵', 'otp_success' => '📱',
            'profile_update_failed' => '⚠️', 'profile_update_success' => '⚙️',
            'file_upload_failed' => '❌', 'file_upload_success' => '🖼️',
            'user_deleted_by_admin' => '🗑️', 'novel_deleted_by_admin' => '🗑️',
            'admin_action' => '👑', 'suspicious_activity' => '☠️',
        ];
        return $icons[$logType] ?? '📝';
    }

    public static function getLogLabel($logType)
    {
        $labels = [
            'login_failed' => 'Login Gagal', 'login_success' => 'Login Berhasil',
            'register_failed' => 'Registrasi Gagal', 'register_success' => 'Registrasi Berhasil',
            'logout' => 'Logout', 'otp_failed' => 'OTP Gagal', 'otp_success' => 'OTP Berhasil',
            'suspicious_activity' => 'Aktivitas Mencurigakan', 'payment_failed' => 'Pembayaran Gagal',
            'payment_success' => 'Pembayaran Berhasil', 'file_upload_failed' => 'Upload Gagal',
            'file_upload_success' => 'Upload Berhasil', 'profile_update_failed' => 'Update Profil Gagal',
            'profile_update_success' => 'Update Profil Berhasil', 'email_verification_failed' => 'Verifikasi Email Gagal',
            'email_verification_success' => 'Verifikasi Email Berhasil', 'password_reset_failed' => 'Reset Password Gagal',
            'password_reset_success' => 'Reset Password Berhasil', 'user_deleted_by_admin' => 'User Dihapus Admin',
            'novel_deleted_by_admin' => 'Novel Dihapus Admin', 'admin_action' => 'Aksi Admin',
        ];
        return $labels[$logType] ?? ucfirst(str_replace('_', ' ', $logType));
    }

    // Scopes
    public function scopeByLogType($query, $type) { return $query->where('log_type', $type); }
    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopeDateRange($query, $startDate, $endDate) {
        return $query->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
    }
    public function scopeSearch($query, $keyword) {
        return $query->where(function($q) use ($keyword) {
            $q->where('email', 'like', "%{$keyword}%")
              ->orWhere('ip_address', 'like', "%{$keyword}%")
              ->orWhere('message', 'like', "%{$keyword}%")
              ->orWhere('user_agent', 'like', "%{$keyword}%");
        });
    }

    public static function deleteOldLogs($days = 30) {
        return self::where('created_at', '<', now()->subDays($days))->delete();
    }
}