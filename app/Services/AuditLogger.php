namespace App\Services;

use Illuminate\Support\Facades\Log;

class AuditLogger
{
    public static function log($action, $user, $resource = null, $details = [])
    {
        Log::channel('audit')->info($action, [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'resource' => $resource,
            'details' => $details,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}

// Usage in controllers
AuditLogger::log('note.created', $request->user(), $note->id);
AuditLogger::log('note.deleted', $request->user(), $note->id);