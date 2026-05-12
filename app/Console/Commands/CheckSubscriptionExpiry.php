<?php

namespace App\Console\Commands;

use App\Models\saas\Subscription;
use Illuminate\Console\Command;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscription-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تحديث حالة الاشتراكات المنتهية والتنبيه بقرب الانتهاء';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredUpdated = Subscription::query()
            ->where('status', '!=', Subscription::STATUS_EXPIRED)
            ->whereDate('end_at', '<', now())
            ->update(['status' => Subscription::STATUS_EXPIRED]);

        $expiringSoon = Subscription::query()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->whereDate('end_at', '>=', now())
            ->whereDate('end_at', '<=', now()->addDays(7))
            ->count();

        $this->info("Expired subscriptions updated: {$expiredUpdated}");
        $this->info("Expiring soon subscriptions: {$expiringSoon}");

        return self::SUCCESS;
    }
}
