<?php

declare(strict_types=1);

namespace App\Actions\Crypto;

use App\Contracts\Crypto\ExchangePriceInterface;
use App\Contracts\Crypto\GatewayProviderInterface;
use App\Dtos\Crypto\SellCryptoDto;
use App\Enums\SettingsEnum;
use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SellCryptoAction
{
    public function __construct(protected GatewayProviderInterface $provider, protected ExchangePriceInterface $exchangePrice) {}

    public function handle(SellCryptoDto $dto): Collection
    {
        $reference = generate_reference();

        $user = request()->user();

        $exchangeRate = Setting::where('name', SettingsEnum::EXCHANGE_RATE->value)->first()->value;

        if (! $exchangeRate || data_get($exchangeRate, 'NGN', 0) === 0) {
            throw new BadRequestHttpException('Exchange rate has not been set by the admin');
        }

        $coinDetail = $this->exchangePrice->getCoinDetail($dto->currency);

        if ($coinDetail->get('price', 0) === 0) {
            throw new BadRequestHttpException('Unable to process transaction at the moment');
        }

        $response = $this->makeApiRequest($reference, $dto, $user);

        DB::transaction(function () use ($user, $coinDetail, $dto, $reference, $exchangeRate) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'amount' => bcmath('mul', [(bcmath('mul', [$coinDetail->get('price'), $dto->amount], 2)), $exchangeRate['NGN']], 2),
                'reference' => $reference,
                'status' => StatusEnum::PENDING,
                'transaction_type' => TransactionTypeEnum::CRYPTO,
                'description' => "Crypto deposit for user {$user->fullname} of {$dto->amount} {$dto->currency}",
            ]);

            $transaction->cryptoTransactions()->create([
                'user_id' => $transaction->user_id,
                'transaction_id' => $transaction->id,
                'provider_name' => 'OxProcessing',
                'reference' => $reference,
                'payload' => null,
                'currency' => $dto->currency,
                'amount' => $dto->amount,
                'payment_method' => $dto->payment_method,
                //  'bank_id' => $dto->bank_id ?? null,
            ]);
        });

        return collect([
            'redirect_url' => $response->get('redirectUrl'),
        ]);
    }

    private function calculateAmount($amount, $currency)
    {
        return $amount;
        $coinDetail = $this->exchangePrice->getCoinDetail($currency);

        if (! $coinDetail->get('processing_fee') && ! $coinDetail->get('network_fee')) {
            throw new BadRequestHttpException('Unable to process transaction at the moment');
        }

        $charges = $coinDetail->get('processing_fee') + $coinDetail->get('network_fee');

        return $amount - $charges;
    }

    private function makeApiRequest(string $reference, SellCryptoDto $dto, User $user)
    {
        $name = explode(' ', $user->fullname);

        $data = [
            'Amount' => (float) $this->calculateAmount($dto->amount, $dto->currency),
            'Currency' => $dto->currency,
            'Email' => $user->email,
            'FirstName' => $name[0],
            'LastName' => $name[1] ?? '',
            'ClientId' => $user->ulid,
            'MerchantId' => config('services.oxprocessing.merchant_id'),
            'BillingID' => $reference,
            'Test' => 'true', // change to false before production
            'ReturnUrl' => 'true',
        ];

        return $this->provider->processTransaction($data);
    }
}
