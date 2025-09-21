<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use App\Contracts\Crypto\ExchangePriceInterface;
use App\Enums\SettingsEnum;
use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Jobs\CreditAccountAfterCyptoSaleJob;
use App\Mail\CryptoSaleMail;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class OxProcessingService extends AbstractProvider implements ExchangePriceInterface
{
    public function getCoinDetails(string $symbol): Collection
    {
        return collect([]);
    }

    public function processTransaction(array $data): Collection
    {
        $response = Http::asForm()->post(config('services.oxprocessing.base_url').'/Payment', $data);

        if (! $response->successful()) {
            $this->errorHandler($response);
        }

        return $response->collect();
    }

    public function getCachedExchangePrice(array $symbols = []): Collection
    {
        $cacheKey = 'exchange_price_oxprocessing';

        return Cache::remember($cacheKey, 60 * 5, fn () => $this->getExchangePrice($symbols));
    }

    public function getCachedCoinLists(): Collection
    {
        $cacheKey = 'coin_lists_oxprocessing';

        return Cache::remember($cacheKey, 60 * 60, function () {
            $response = Http::withHeaders(['APIKEY' => config('services.oxprocessing.api_key')])->acceptJson()->get(config('services.oxprocessing.base_url').'/Api/Coins');

            if (! $response->successful()) {
                $this->errorHandler($response);
            }

            return $response->collect();
        });
    }

    public function getExchangePrice(array $symbols = []): Collection
    {
        if (count($symbols) === 0) {
            $symbols = ['BTC'];
        }

        $last_updated = now()->format('Y-m-d H:i:s');

        $func = collect($symbols)->map(function ($symbol) use ($last_updated) {
            return fn () => [
                'symbol' => $symbol,
                'data' => $this->get('/Api/ConvertCryptoToFiat', [
                    'InAmount' => 1,
                    'InCurrency' => $symbol,
                    'OutCurrency' => 'USD',
                ])->collect('result'),
                'last_updated' => $last_updated,
            ];
        });

        $response = Concurrency::run($func->all());

        return collect($response)->map(fn ($item) => [
            'symbol' => $item['symbol'],
            'price' => $item['data'][0] ?? 0,
            'last_updated' => $item['last_updated'],
        ]);
    }

    public function getCoinDetail(string $coin): Collection
    {
        $symbols = Setting::where('name', SettingsEnum::SUPPORTED_COINS->value)->first()->value ?? [];

        $data = $this->getCachedExchangePrice($symbols);

        $coinPrice = $data->where('symbol', $coin)->first();

        if (! $coinPrice) {
            throw new BadRequestHttpException('Unsupported symbol');
        }

        $response = $this->getCachedCoinLists();

        $detail = collect($response->where('currency', $coin)->first());

        return collect([
            'symbol' => $coinPrice['symbol'],
            'min' => $detail->get('min'),
            'max' => $detail->get('max'),
            'waiting_time' => $detail->get('waitingTime'),
            'processing_fee' => $detail->get('processingFee'),
            'network_fee' => $detail->get('networkFee'),
            'price' => $coinPrice['price'] ?? 0,
        ]);
    }

    public function processWebhook(string $eventName, Collection $data): bool
    {
        return match ($eventName) {
            TransactionTypeEnum::CRYPTO->value => $this->processCryptoWebhook($data),
            default => false,
        };
    }

    private function processCryptoWebhook(Collection $data): bool
    {
        $transaction = Transaction::with('user')->where('reference', $data->get('BillingID'))->first();

        if (! $transaction) {
            return false;
        }

        $cryptoTransaction = null;

        DB::transaction(function () use ($transaction, $data, &$cryptoTransaction) {
            $transaction->update([
                'status' => get_status($data->get('Status')),
            ]);
            $cryptoTransaction = $transaction->cryptoTransactions()->where('reference', $data->get('BillingID'))->first();
            $cryptoTransaction->update([
                'status' => get_status($data->get('Status')),
                'provider_reference' => $data->get('PaymentId'),
                'payload' => $data->toArray(),
            ]);
        });

        // process payment if success
        if (get_status($data->get('Status')) === StatusEnum::SUCCESS->value) {
            CreditAccountAfterCyptoSaleJob::dispatch($transaction, $cryptoTransaction);
        }

        if (get_status($data->get('Status')) === StatusEnum::PENDING->value) {
            return true;
        }

        // send email to user
        if ($cryptoTransaction) {
            Mail::to($transaction->user->email)->send(new CryptoSaleMail($transaction->user->username, $transaction, $cryptoTransaction));
        }

        return true;
    }

    private function get(string $url, ?array $payload = []): Response
    {
        $response = Http::acceptJson()->withToken(config('services.oxprocessing.api_key'))->get(config('services.oxprocessing.base_url').$url, [
            ...$payload,
        ]);

        if (! $response->successful()) {
            $this->errorHandler($response);
        }

        return $response;
    }
}
