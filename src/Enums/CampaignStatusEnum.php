<?php

namespace Botble\Mailing\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static CampaignStatusEnum DRAFT()
 * @method static CampaignStatusEnum SCHEDULED()
 * @method static CampaignStatusEnum SENDING()
 * @method static CampaignStatusEnum SENT()
 * @method static CampaignStatusEnum CANCELLED()
 */
class CampaignStatusEnum extends Enum
{
    public const DRAFT = 'draft';
    public const SCHEDULED = 'scheduled';
    public const SENDING = 'sending';
    public const SENT = 'sent';
    public const CANCELLED = 'cancelled';

    public static $langPath = 'plugins/mailing::mailing.statuses';

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::DRAFT => 'secondary',
            self::SCHEDULED => 'info',
            self::SENDING => 'warning',
            self::SENT => 'success',
            self::CANCELLED => 'danger',
            default => 'secondary',
        };

        return Html::tag('span', $this->label(), ['class' => 'badge bg-' . $color]);
    }
}
