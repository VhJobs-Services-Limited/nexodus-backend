<?php

namespace App\Services\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Enums\BillProviderEnum;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ClubConnectService extends AbstractProvider implements BillProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getProviderName(): string
    {
        return BillProviderEnum::ClubConnect->value;
    }

    /**
     * {@inheritDoc}
     */
    public function getWalletBalance(): mixed
    {
        $response = $this->get('/APIWalletBalanceV1.asp');

        return (float) str_replace(',', '', $response->collect()->get('balance') ?? '0');
    }

    /**
     * {@inheritDoc}
     */
    public function getAirtimeProviders(): Collection
    {
        $response = $this->get('/APIAirtimeDiscountV2.asp');

        return collect($response->collect()->get('MOBILE_NETWORK'))->flatten(1)->map(fn ($item) => [
          'id' => $item['ID'],
          'name' => $item['PRODUCT_NAME'],
          'image_url' => provider_image_url($item['PRODUCT_NAME']),
      ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getDataProviders(): Collection
    {
        $response = $this->get('/APIDatabundlePlansV2.asp');

        return collect($response->collect()->get('MOBILE_NETWORK'))
                    ->map(function ($networkProducts, $networkName) {
                        $networkGroup = $networkProducts[0];
                        $networkName = str_replace('m_', '', $networkName);
                        return [
                            'id' => $networkGroup['ID'],
                            'name' => $networkName,
                            'image_url' => provider_image_url($networkName),
                            'products' => collect($networkGroup['PRODUCT'])->map(fn ($product) => [
                                'id' => $product['PRODUCT_ID'],
                                'name' => $product['PRODUCT_NAME'],
                                'code' => $product['PRODUCT_CODE'],
                                'amount' => $product['PRODUCT_AMOUNT'],
                              ])->values()->all(),
                          ];
                    });
    }

    /**
     * {@inheritDoc}
     */
    public function getCableProviders(): Collection
    {
        $response = $this->get('/APICableTVPackagesV2.asp');

        return collect($response->collect()->get('TV_ID'))
                          ->map(function ($networkProducts, $networkName) {
                              $networkGroup = $networkProducts[0];
                              return [
                                  'id' => $networkGroup['ID'],
                                  'name' => $networkName,
                                  'image_url' => provider_image_url($networkName),
                                  'packages' => collect($networkGroup['PRODUCT'])->map(fn ($product) => [
                                      'id' => $product['PACKAGE_ID'],
                                      'name' => $product['PACKAGE_NAME'],
                                      'amount' => $product['PACKAGE_AMOUNT'],
                                    ])->values()->all(),
                                ];
                          });
    }

    /**
     * {@inheritDoc}
     */
    public function getWifiProviders(): Collection
    {
        return collect();
    }

    /**
     * {@inheritDoc}
     */
    public function getElectricityProviders(): Collection
    {
        $response = $this->get('/APIElectricityDiscosV1.asp');
        return collect($response->collect()->get('ELECTRIC_COMPANY'))
           ->map(function ($networkProducts, $networkName) {
               $networkGroup = $networkProducts[0];
               return [
                   'id' => $networkGroup['ID'],
                   'name' => str_replace('_', ' ', $networkName),
                   'products' => collect($networkGroup['PRODUCT'])->map(fn ($product) => [
                       'id' => $product['PRODUCT_ID'],
                       'type' => $product['PRODUCT_TYPE'],
                     ])->values()->all(),
                 ];
           });
    }

    public function getBettingProviders(): Collection
    {
        $response = $this->get('/APIBettingCompaniesV2.asp');
        return collect($response->collect()->get('BETTING_COMPANY'))->map(fn ($item) => [
            'id' => $item['PRODUCT_CODE'],
            'name' => str($item['PRODUCT_CODE'])->title()->toString(),
        ]);
    }

    protected function get(string $url, ?array $payload = []): Response
    {
        $response = Http::acceptJson()->get(config('services.clubconnect.base_url').$url, [
            ...$payload,
            'UserID' => config('services.clubconnect.user_id'),
            'APIKey' => config('services.clubconnect.api_key'),
        ]);

        if (! $response->successful()) {
            $this->errorHandler($response);
        }

        return $response;
    }
}
