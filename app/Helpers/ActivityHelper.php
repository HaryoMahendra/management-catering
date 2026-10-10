<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityHelper
{
    public static function record($activity, $description)
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
}