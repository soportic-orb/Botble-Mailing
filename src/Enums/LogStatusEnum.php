<?php

namespace Botble\Mailing\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static LogStatusEnum PENDING()
 * @method static LogStatusEnum SENT()
 * @method static LogStatusEnum FAILED()
 */
class LogStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const SENT = 'sent';
    public const FAILED = 'failed';

    public static $langPath = 'plugins/mailing::mailing.log_statuses';

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::PENDING => 'secondary',
            self::SENT => 'success',
            self::FAILED => 'danger',
            default => 'secondary',
        };

        return Html::tag('span', $this->label(), ['class' => 'badge bg-' . $color]);
    }
}
