<?php

declare(strict_types=1);

use App\Contracts\Bill\BillProviderInterface;
use App\Jobs\ProcessPendingClubConnectRequestJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\WebhookClient\Models\WebhookCall;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('nexodus', function () {
    $billProvider = app(BillProviderInterface::class);
    $airtimeProviders = $billProvider->getCableList();
    $this->info('Airtime providers: '.json_encode($airtimeProviders));
})->purpose('Display the airtime providers');

Schedule::command('queue:work --queue=payments,notifications,default')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::job(new ProcessPendingClubConnectRequestJob())
    ->everyFifteenMinutes()
    ->withoutOverlapping();

Schedule::command('model:prune', [
    '--model' => [WebhookCall::class],
])->daily();
