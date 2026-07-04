<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\LogOptions;

trait LogsPortalActivity
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getTable());
    }
}
