<?php

namespace Botble\Mailing\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static CampaignTypeEnum MANUAL()
 * @method static CampaignTypeEnum POST_PUBLISHED()
 * @method static CampaignTypeEnum MONTHLY_DIGEST()
 */
class CampaignTypeEnum extends Enum
{
    public const MANUAL = 'manual';
    public const POST_PUBLISHED = 'post_published';
    public const MONTHLY_DIGEST = 'monthly_digest';

    public static $langPath = 'plugins/mailing::mailing.types';

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::MANUAL => 'primary',
            self::POST_PUBLISHED => 'cyan',
            self::MONTHLY_DIGEST => 'purple',
            default => 'secondary',
        };

        return Html::tag('span', $this->label(), ['class' => 'badge bg-' . $color]);
    }
}
