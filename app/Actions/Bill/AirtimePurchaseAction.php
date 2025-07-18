<?php

namespace App\Actions\Bill;

use App\DTOs\Bill\BaseBillDto;
use App\Enums\BillEnum;
use App\Models\BillTransaction;

class AirtimePurchaseAction extends BaseBillAction
{
    public function handle(array $dto): BillTransaction
    {
        $billTransaction = $this->createBillTransaction(BaseBillDto::fromArray([
            'amount' => $dto['amount'],
            'type' => BillEnum::AIRTIME,
            'payload' => $dto,
            'description' => 'Airtime purchase',
        ]));

        return $this->provider->billPurchase($billTransaction, fn () => $this->provider->purchaseAirtime($billTransaction));
    }
}
