<?php

namespace Botble\Mailing\Http\Controllers;

use Botble\Mailing\Enums\ContactStatusEnum;
use Botble\Mailing\Models\Campaign;
use Botble\Mailing\Models\Contact;
use Botble\Mailing\Models\MailingLog;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class PublicTrackingController extends Controller
{
    /**
     * Open-tracking pixel: a 1x1 transparent gif embedded in every email.
     */
    public function trackOpen(string $token): Response
    {
        $log = MailingLog::query()->where('token', $token)->first();

        if ($log && ! $log->opened_at) {
            $log->forceFill(['opened_at' => Carbon::now()])->save();

            Campaign::query()->where('id', $log->campaign_id)->increment('opened_count');
        }

        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function unsubscribe(string $token)
    {
        $contact = Contact::query()->where('token', $token)->first();

        if ($contact) {
            $contact->forceFill(['status' => ContactStatusEnum::UNSUBSCRIBED])->save();
        }

        return response()->view('plugins/mailing::unsubscribe', [
            'success' => (bool) $contact,
        ]);
    }
}
