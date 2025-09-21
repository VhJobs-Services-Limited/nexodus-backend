<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use App\Contracts\Crypto\ExchangePriceInterface;
use App\Traits\HttpErrorHandler;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class FixerApiService implements ExchangePriceInterface
{
    use HttpErrorHandler;

    public function getCachedExchangePrice(array $symbols = []): Collection
    {
        $cacheKey = 'exchange_price_'.implode('_', $symbols);
        // $data = Cache::remember($cacheKey, 60 * 30, fn () => $this->getExchangePrice());
        $data = $this->getExchangePrice($symbols);

        return $data;
        // return $data->get('rates')->map(fn ($item) => [
        //       'symbol' => $item['symbol'],
        //       'price' => $item,
        //       'last_updated' => $data->get('date'),
        //     ])->values();
    }

    public function getExchangePrice(array $symbols = []): Collection
    {
        $symbols = count($symbols) > 0 ? $symbols : ['BTC', 'ETH', 'USDT', 'USDC'];

        $response = $this->get('/latest', ['base' => 'USD', 'symbols' => implode(',', $symbols)])->collect();

        logger('response: ', [$response->get('rates')]);

        return collect(['rates' => $response->get('rates'), 'date' => $response->get('date')]);
    }

    public function getCachedCoinLists(): Collection
    {
        return collect();
    }

    public function getCoinDetail(string $string): Collection
    {
        return collect();
    }

    private function get(string $url, ?array $payload = []): Response
    {
        $response = Http::acceptJson()->get(config('services.fixer.base_url').$url, [
            ...$payload,
            'access_key' => config('services.fixer.api_key'),
        ]);

        if (! $response->successful()) {
            $this->errorHandler($response);
        }

        return $response;
    }
}
