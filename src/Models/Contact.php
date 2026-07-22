<?php

namespace Botble\Mailing\Models;

use Botble\Base\Models\BaseModel;
use Botble\Mailing\Enums\ContactStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Contact extends BaseModel
{
    protected $table = 'mailing_contacts';

    protected $fillable = [
        'name',
        'email',
        'status',
        'token',
    ];

    protected $casts = [
        'status' => ContactStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $contact): void {
            if (! $contact->token) {
                $contact->token = Str::random(32);
            }
        });
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MailingLog::class, 'contact_id');
    }
}
