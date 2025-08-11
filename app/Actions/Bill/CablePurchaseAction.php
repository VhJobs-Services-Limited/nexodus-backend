<?php

namespace App\Actions\Bill;

use App\Dtos\Bill\BaseBillDto;
use App\Enums\BillEnum;
use App\Models\BillTransaction;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CablePurchaseAction extends BaseBillAction
{
    /**
     * Create a new class instance.
     */
    public function handle(array $dto): BillTransaction
    {
        $cableList = $this->provider->getCableList();
        $plan = collect(collect($cableList->firstWhere('id', $dto['provider_id']))->get('packages'))->firstWhere('id', $dto['package_id']);

        if (!$plan) {
            throw new BadRequestHttpException('Plan not found');
        }

        $amount = $plan['amount'];

        $billTransaction = $this->createBillTransaction(BaseBillDto::fromArray([
                  'amount' => $amount,
                  'type' => BillEnum::CABLE,
                  'payload' => $dto,
                  'description' => 'Cable purchase',
              ]));

        return $this->provider->billPurchase($billTransaction, fn () => $this->provider->purchaseCable($billTransaction));
    }
}
