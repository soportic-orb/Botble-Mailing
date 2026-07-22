<?php

namespace Botble\Mailing\Commands;

use Botble\Mailing\Enums\CampaignStatusEnum;
use Botble\Mailing\Models\Campaign;
use Botble\Mailing\Services\MailingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessMailingCommand extends Command
{
    protected $signature = 'mailing:process';

    protected $description = 'Promote scheduled campaigns and send pending emails in batches (throttled).';

    public function handle(MailingService $mailingService): int
    {
        // Promote scheduled campaigns whose time has come.
        Campaign::query()
            ->where('status', CampaignStatusEnum::SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', Carbon::now())
            ->get()
            ->each(function (Campaign $campaign) use ($mailingService): void {
                $mailingService->dispatch($campaign);
                $this->info(sprintf('Campaign #%d "%s" started.', $campaign->getKey(), $campaign->name));
            });

        // Process a batch for every campaign currently sending.
        Campaign::query()
            ->where('status', CampaignStatusEnum::SENDING)
            ->get()
            ->each(function (Campaign $campaign) use ($mailingService): void {
                $mailingService->processBatch($campaign);
            });

        return self::SUCCESS;
    }
}
