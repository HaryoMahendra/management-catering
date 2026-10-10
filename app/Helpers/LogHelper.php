<?php
use App\Models\ActivityLog;

function recordActivity($activity, $description)
{
    if (auth()->check()) {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => $activity,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
