<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'package_id', 'destination_id', 'status', 'admin_notes'])]
class Inquiry extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'in_progress' => 'In progress',
        'closed' => 'Closed',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /** What the enquiry is about, for emails and the admin list. */
    public function topic(): ?string
    {
        return $this->package?->name ?? $this->destination?->name;
    }
}
