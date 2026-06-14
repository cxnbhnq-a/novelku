<?php

/**
 * ACTIVITY LOG HELPER FUNCTIONS
 * Fungsi-fungsi helper untuk memudahkan pencatatan activity log
 */

use App\Services\ActivityLogService;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Log activity dengan shortcut function
 * 
 * @param string $logType
 * @param string $message
 * @param string $status
 * @param string $severity
 * @param string|null $email
 * @param string|null $userId
 * @param array|null $payload
 * @param array|null $response
 * @param string|null $stackTrace
 * @param Request|null $request
 * @return void
 */
if (!function_exists('logActivity')) {
    function logActivity(
        $logType,
        $message,
        $status = 'success',
        $severity = 'info',
        $email = null,
        $userId = null,
        $payload = null,
        $response = null,
        $stackTrace = null,
        $request = null
    ) {
        ActivityLogService::log(
            $logType,
            $message,
            $status,
            $severity,
            $email,
            $userId,
            $payload,
            $response,
            $stackTrace,
            $request
        );
    }
}

/**
 * Log failed login
 */
if (!function_exists('logLoginFailed')) {
    function logLoginFailed($email, $reason = 'Invalid credentials', $request = null)
    {
        ActivityLogService::logLoginFailed($email, $reason, $request);
    }
}

/**
 * Log successful login
 */
if (!function_exists('logLoginSuccess')) {
    function logLoginSuccess($user, $request = null)
    {
        ActivityLogService::logLoginSuccess($user, $request);
    }
}

/**
 * Log failed OTP
 */
if (!function_exists('logOtpFailed')) {
    function logOtpFailed($email, $reason = 'Invalid OTP', $request = null)
    {
        ActivityLogService::logOtpFailed($email, $reason, $request);
    }
}

/**
 * Log successful OTP
 */
if (!function_exists('logOtpSuccess')) {
    function logOtpSuccess($email, $request = null)
    {
        ActivityLogService::logOtpSuccess($email, $request);
    }
}

/**
 * Log failed registration
 */
if (!function_exists('logRegisterFailed')) {
    function logRegisterFailed($email, $reason = 'Validation failed', $request = null)
    {
        ActivityLogService::logRegisterFailed($email, $reason, $request);
    }
}

/**
 * Log successful registration
 */
if (!function_exists('logRegisterSuccess')) {
    function logRegisterSuccess($user, $request = null)
    {
        ActivityLogService::logRegisterSuccess($user, $request);
    }
}

/**
 * Log failed profile update
 */
if (!function_exists('logProfileUpdateFailed')) {
    function logProfileUpdateFailed($user, $reason = 'Update failed', $request = null)
    {
        ActivityLogService::logProfileUpdateFailed($user, $reason, $request);
    }
}

/**
 * Log successful profile update
 */
if (!function_exists('logProfileUpdateSuccess')) {
    function logProfileUpdateSuccess($user, $changes = null, $request = null)
    {
        ActivityLogService::logProfileUpdateSuccess($user, $changes, $request);
    }
}

/**
 * Log failed file upload
 */
if (!function_exists('logFileUploadFailed')) {
    function logFileUploadFailed($user, $reason = 'Upload failed', $request = null)
    {
        ActivityLogService::logFileUploadFailed($user, $reason, $request);
    }
}

/**
 * Log successful file upload
 */
if (!function_exists('logFileUploadSuccess')) {
    function logFileUploadSuccess($user, $fileName = null, $request = null)
    {
        ActivityLogService::logFileUploadSuccess($user, $fileName, $request);
    }
}

/**
 * Log failed payment
 */
if (!function_exists('logPaymentFailed')) {
    function logPaymentFailed($user, $reason = 'Payment failed', $request = null)
    {
        ActivityLogService::logPaymentFailed($user, $reason, $request);
    }
}

/**
 * Log successful payment
 */
if (!function_exists('logPaymentSuccess')) {
    function logPaymentSuccess($user, $transactionId = null, $request = null)
    {
        ActivityLogService::logPaymentSuccess($user, $transactionId, $request);
    }
}

/**
 * Log logout
 */
if (!function_exists('logLogout')) {
    function logLogout($user, $request = null)
    {
        ActivityLogService::logLogout($user, $request);
    }
}

/**
 * Log suspicious activity
 */
if (!function_exists('logSuspiciousActivity')) {
    function logSuspiciousActivity($message, $email = null, $request = null)
    {
        ActivityLogService::logSuspiciousActivity($message, $email, $request);
    }
}

/**
 * Get activity log color
 */
if (!function_exists('getLogColor')) {
    function getLogColor($logType)
    {
        return ActivityLog::getLogColor($logType);
    }
}

/**
 * Get activity log label
 */
if (!function_exists('getLogLabel')) {
    function getLogLabel($logType)
    {
        return ActivityLog::getLogLabel($logType);
    }
}

/**
 * Get activity log icon
 */
if (!function_exists('getLogIcon')) {
    function getLogIcon($logType)
    {
        return ActivityLog::getLogIcon($logType);
    }
}
