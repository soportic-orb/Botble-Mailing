<?php

namespace Botble\Mailing\Models;

use Botble\Base\Models\BaseModel;
use Botble\Mailing\Enums\LogStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MailingLog extends BaseModel
{
    protected $table = 'mailing_logs';

    protected $fillable = [
        'campaign_id',
        'contact_id',
        'email',
        'status',
        'sent_at',
        'opened_at',
        'error',
        'token',
    ];

    protected $casts = [
        'status' => LogStatusEnum::class,
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log): void {
            if (! $log->token) {
                $log->token = Str::random(32);
            }
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
