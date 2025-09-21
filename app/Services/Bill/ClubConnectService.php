<?php

declare(strict_types=1);

namespace App\Services\Bill;

use App\Contracts\Bill\BillProviderInterface;
use App\Enums\BillProviderEnum;
use App\Exceptions\ThirdPartyExecption;
use App\Jobs\RefundJob;
use App\Models\BillTransaction;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;

final class ClubConnectService extends AbstractProvider implements BillProviderInterface
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
    public function getAirtimeList(): Collection
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
    public function getDataList(): Collection
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
                        'amount' => floor((float) $product['PRODUCT_ID']),
                    ])->values()->all(),
                ];
            });
    }

    /**
     * {@inheritDoc}
     */
    public function getCableList(): Collection
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
    public function getWifiList(): Collection
    {
        $wifis = [
            fn () => [
                'source' => 'smile',
                'data' => $this->get('/APISmilePackagesV2.asp')->collect(),
            ],
            fn () => [
                'source' => 'spectranet',
                'data' => $this->get('/APISpectranetPackagesV2.asp')->collect(),
            ],
        ];

        $results = Concurrency::run($wifis);
        $formattedResults = collect($results)->map(function ($result) {
            $data = match ($result['source']) {
                'smile' => collect($result['data']->get('MOBILE_NETWORK'))->flatten(1)->map(fn ($item) => [
                    'id' => $item['ID'],
                    'image_url' => provider_image_url($result['source']),
                    'name' => $item['ID'],
                    'products' => collect($item['PRODUCT'])->filter(fn ($product) => $product['PACKAGE_ID'] !== 'airtime')->map(fn ($product) => [
                        'id' => $product['PACKAGE_ID'],
                        'name' => $product['PACKAGE_NAME'],
                        'amount' => $product['PACKAGE_AMOUNT'],
                    ])->values()->toArray(),
                ])->first(),
                'spectranet' => collect($result['data']->get('MOBILE_NETWORK'))->flatten(1)->map(fn ($item) => [
                    'id' => $item['ID'],
                    'image_url' => provider_image_url($result['source']),
                    'name' => $item['ID'],
                    'products' => collect($item['PRODUCT'])->map(fn ($product) => [
                        'id' => $product['PACKAGE_ID'],
                        'name' => $product['PACKAGE_NAME'],
                        'amount' => $product['PACKAGE_AMOUNT'],
                    ]),
                ])->first(),
            };

            return [$result['source'] => $data];
        });

        return collect($formattedResults->first())->merge(collect($formattedResults->last()));
    }

    /**
     * {@inheritDoc}
     */
    public function getElectricityList(): Collection
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

    public function getBettingList(): Collection
    {
        $response = $this->get('/APIBettingCompaniesV2.asp');

        return collect($response->collect()->get('BETTING_COMPANY'))->map(fn ($item) => [
            'id' => $item['PRODUCT_CODE'],
            'name' => str($item['PRODUCT_CODE'])->title()->toString(),
        ]);
    }

    public function billPurchase(BillTransaction $billTransaction, callable $responseFn): BillTransaction
    {
        try {
            $response = $responseFn();

            logger()->info('purchase response', ['response' => $response]);

            if (! $response->has('orderid')) {
                RefundJob::dispatch($billTransaction->transaction);
                throw new ThirdPartyExecption($response->get('status', 'We are unable to process your request at the moment, please try again later'));
            }

            return tap($billTransaction, fn ($billTransaction) => $billTransaction->update(['provider_reference' => $response->get('orderid')]));
        } catch (Throwable $th) {
            if ($th instanceof ThirdPartyExecption) {
                throw $th;
            }

            logger()->error('purchase error', ['error' => $th]);
            RefundJob::dispatch($billTransaction->transaction);

            throw new ServiceUnavailableHttpException(100, 'We are unable to process your request at the moment, please try again later');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function purchaseAirtime(BillTransaction $billTransaction): Collection
    {
        logger()->info('purchase airtime', ['billTransaction' => $billTransaction]);
        $payload = [
            'MobileNetwork' => $billTransaction->payload['provider_id'],
            'Amount' => $billTransaction->amount,
            'MobileNumber' => $billTransaction->payload['phone_number'],
            'RequestID' => $billTransaction->reference,
            'CallBackURL' => config('services.clubconnect.callback_url'),
        ];

        $response = $this->get('/APIAirtimeV1.asp', $payload);

        return $response->collect();
    }

    public function purchaseData(BillTransaction $billTransaction): Collection
    {
        $payload = [
            'MobileNetwork' => $billTransaction->payload['provider_id'],
            'DataPlan' => $billTransaction->payload['data_id'],
            'Amount' => $billTransaction->amount,
            'MobileNumber' => $billTransaction->payload['phone_number'],
            'RequestID' => $billTransaction->reference,
            'CallBackURL' => config('services.clubconnect.callback_url'),
        ];

        $response = $this->get('/APIDatabundleV1.asp', $payload);

        return $response->collect();
    }

    public function verifyBettingAccountId(string $bettingProvider, string $accountId): Collection
    {
        $response = $this->get('/APIVerifyBettingV1.asp', ['BettingCompany' => $bettingProvider, 'CustomerID' => $accountId]);

        return $response->collect();
    }

    public function purchaseBetting(BillTransaction $billTransaction): Collection
    {
        $response = $this->get('/APIBettingV1.asp', ['BettingCompany' => $billTransaction->payload['provider_id'], 'CustomerID' => $billTransaction->payload['account_id'], 'Amount' => $billTransaction->amount,  'CallBackURL' => config('services.clubconnect.callback_url')]);

        logger()->info('purchase betting response', ['response' => $response->collect()]);

        return $response->collect();
    }

    public function verifyMetreNumber(string $electricityProvider, string $metreNumber): Collection
    {
        $response = $this->get('/APIVerifyElectricityV1.asp', ['ElectricCompany' => $electricityProvider, 'MeterNo' => $metreNumber]);

        return $response->collect();
    }

    public function purchaseElectricity(BillTransaction $billTransaction): Collection
    {
        $response = $this->get('/APIElectricityV1.asp', ['ElectricCompany' => $billTransaction->payload['provider_id'], 'MeterType' => $billTransaction->payload['metre_type'], 'MeterNo' => $billTransaction->payload['metre_number'], 'Amount' => round($billTransaction->amount), 'PhoneNo' => $billTransaction->payload['phone_number'], 'CallBackURL' => config('services.clubconnect.callback_url')]);

        return $response->collect();
    }

    public function verifySmileDeviceId(string $providerId, string $deviceId): Collection
    {
        $response = $this->get('/APIVerifySmileV1.asp', ['MobileNetwork' => $providerId, 'MobileNumber' => $deviceId]);

        return $response->collect();
    }

    public function purchaseWifi(BillTransaction $billTransaction): Collection
    {
        $data = match ($billTransaction->payload['provider_id']) {
            'smile-direct' => [
                'url' => '/APISmileV1.asp',
                'params' => [
                    'MobileNetwork' => 'smile-direct',
                    'datatplan' => $billTransaction->payload['plan_id'],
                    'MobileNumber' => $billTransaction->payload['device_id'],
                    'CallBackURL' => config('services.clubconnect.callback_url'),
                ],
            ],
            'spectranet' => [
                'url' => '/APISpectranetV1.asp',
                'params' => [
                    'MobileNetwork' => 'spectranet',
                    'datatplan' => $billTransaction->payload['plan_id'],
                    'MobileNumber' => $billTransaction->payload['device_id'],
                    'CallBackURL' => config('services.clubconnect.callback_url'),
                ],
            ],
        };
        $response = $this->get($data['url'], $data['params']);

        return $response->collect();
    }

    public function purchaseCable(BillTransaction $billTransaction): Collection
    {
        $response = $this->get('/APICableTVV1.asp', [
            'CableTV' => $billTransaction->payload['provider_id'],
            'Package' => $billTransaction->payload['package_id'],
            'SmartCardNo' => $billTransaction->payload['smart_card_number'],
            'PhoneNo' => $billTransaction->payload['phone_number'],
            'CallBackURL' => config('services.clubconnect.callback_url'),
        ]);

        return $response->collect();
    }

    public function verifyCableSmartCard(string $providerId, string $smartCardNumber): Collection
    {
        $response = $this->get('/APIVerifyCableTVV1.0.asp', ['cabletv' => $providerId, 'smartcardno' => $smartCardNumber]);

        return $response->collect();
    }

    public function getOrder(string $orderId): Collection
    {
        $response = $this->get('/APIQueryV1.asp', ['OrderID' => $orderId]);

        return $response->collect();
    }

    public function cancelOrder(string $orderId): Collection
    {
        $response = $this->get('/APICancelV1.asp', ['OrderID' => $orderId]);

        return $response->collect();
    }

    public function getPercentageCharge(): float
    {
        // 01 for MTN @ 3%

        // 02 for GLO @ 8%

        // 04 for Airtel @ 3.2%

        // 03 for 9mobile @ 7%

        return 0.05;
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
