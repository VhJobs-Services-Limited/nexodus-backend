<?php

declare(strict_types=1);

namespace App\Services\Crypto;

use App\Contracts\Crypto\ExchangePriceInterface;
use App\Traits\HttpErrorHandler;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class FreeCryptoApiService implements ExchangePriceInterface
{
    use HttpErrorHandler;

    public function getCachedExchangePrice(array $symbols = []): Collection
    {
        $transformedSymbols = array_map(fn ($symbol) => mb_strtoupper("{$symbol}USDT"), $symbols);

        $cacheKey = 'exchange_price_'.implode('_', $transformedSymbols);

        $data = Cache::remember($cacheKey, 60 * 5, fn () => $this->getExchangePrice());

        return $data->filter(fn ($item) => count($transformedSymbols) > 0 ? in_array(mb_strtoupper($item['symbol']), $transformedSymbols) : true)->map(fn ($item) => [
            'symbol' => $item['symbol'],
            'price' => $item['last'],
            'last_updated' => $item['date'],
        ])->values();
    }

    public function getExchangePrice(array $symbols = []): Collection
    {
        $response = $this->get('/v1/getExchange', ['exchange' => 'binance'])->collect();

        return collect($response->get('symbols'));
    }

    public function getCoinDetail(string $string): Collection
    {
        return collect();
    }

    public function getCachedCoinLists(): Collection
    {
        return collect();
    }

    private function get(string $url, ?array $payload = []): Response
    {
        $response = Http::acceptJson()->withToken(config('services.freecryptoapi.api_key'))->get(config('services.freecryptoapi.base_url').$url, [
            ...$payload,
        ]);

        if (! $response->successful()) {
            $this->errorHandler($response);
        }

        return $response;
    }
}
