<?php

namespace Botble\Mailing\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static ContactStatusEnum SUBSCRIBED()
 * @method static ContactStatusEnum UNSUBSCRIBED()
 */
class ContactStatusEnum extends Enum
{
    public const SUBSCRIBED = 'subscribed';
    public const UNSUBSCRIBED = 'unsubscribed';

    public static $langPath = 'plugins/mailing::mailing.contact_statuses';

    public function toHtml(): HtmlString|string
    {
        $color = $this->value === self::SUBSCRIBED ? 'success' : 'danger';

        return Html::tag('span', $this->label(), ['class' => 'badge bg-' . $color]);
    }
}
