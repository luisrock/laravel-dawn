<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\StripeProductController;

class SyncStripeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Stripe products with the local database';

    /**
     * The StripeProductController instance.
     *
     * @var StripeProductController
     */
    protected $stripeProductController;

    /**
     * Create a new command instance.
     *
     * @param StripeProductController $stripeProductController
     * @return void
     */
    public function __construct(StripeProductController $stripeProductController)
    {
        parent::__construct();

        $this->stripeProductController = $stripeProductController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Syncing Stripe products...');
        $this->stripeProductController->sync();
        $this->info('Stripe products synced successfully.');

        return 0;
    }
}
