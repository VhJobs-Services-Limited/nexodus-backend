<?php

declare(strict_types=1);

namespace App\Actions\Bill;

use App\Dtos\Bill\BaseBillDto;
use App\Enums\BillEnum;
use App\Models\BillTransaction;

final class ElectricityPurchaseAction extends BaseBillAction
{
    public function handle(array $dto): BillTransaction
    {
        $billTransaction = $this->createBillTransaction(BaseBillDto::fromArray([
            'amount' => $dto['amount'],
            'type' => BillEnum::ELECTRICITY,
            'payload' => $dto,
            'description' => 'Electricity purchase',
        ]));

        return $this->provider->billPurchase($billTransaction, fn () => $this->provider->purchaseElectricity($billTransaction));
    }
}
