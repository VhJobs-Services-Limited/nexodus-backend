<?php

namespace App\Actions\Bill;

use App\Dtos\Bill\BaseBillDto;
use App\Enums\BillEnum;
use App\Models\BillTransaction;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WifiPurchaseAction extends BaseBillAction
{
    /**
     * Create a new class instance.
     */
    public function handle(array $dto): BillTransaction
    {
        $wifiList = $this->provider->getWifiList();
        $plan = collect(collect($wifiList->firstWhere('id', $dto['provider_id']))->get('products'))->firstWhere('id', $dto['plan_id']);

        if (!$plan) {
            throw new BadRequestHttpException('Plan not found');
        }

        $amount = $plan['amount'];

        $billTransaction = $this->createBillTransaction(BaseBillDto::fromArray([
                  'amount' => $amount,
                  'type' => BillEnum::WIFI,
                  'payload' => $dto,
                  'description' => 'Wifi purchase',
              ]));

        return $this->provider->billPurchase($billTransaction, fn () => $this->provider->purchaseWifi($billTransaction));
    }
}
