<?php

namespace Botble\Mailing\Models;

use Botble\Base\Models\BaseModel;
use Botble\Mailing\Enums\CampaignStatusEnum;
use Botble\Mailing\Enums\CampaignTypeEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends BaseModel
{
    protected $table = 'mailing_campaigns';

    protected $fillable = [
        'name',
        'subject',
        'content',
        'type',
        'status',
        'post_id',
        'scheduled_at',
        'started_at',
        'completed_at',
        'last_batch_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'opened_count',
    ];

    protected $casts = [
        'type' => CampaignTypeEnum::class,
        'status' => CampaignStatusEnum::class,
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_batch_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(MailingLog::class, 'campaign_id');
    }

    public function isEditable(): bool
    {
        return in_array($this->status->getValue(), [CampaignStatusEnum::DRAFT, CampaignStatusEnum::SCHEDULED]);
    }

    public function isSendable(): bool
    {
        return in_array($this->status->getValue(), [
            CampaignStatusEnum::DRAFT,
            CampaignStatusEnum::SCHEDULED,
            CampaignStatusEnum::CANCELLED,
        ]);
    }
}
