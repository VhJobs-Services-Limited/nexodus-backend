<?php

namespace App\Actions\Bill;

use App\DTOs\Bill\BaseBillDto;
use App\Enums\BillEnum;
use App\Models\BillTransaction;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DataPurchaseAction extends BaseBillAction
{
    /**
     * Create a new class instance.
     */
    public function handle(array $dto): BillTransaction
    {
        $dataList = $this->provider->getDataList();
        $data = collect(collect($dataList->firstWhere('id', $dto['provider_id']))->get('products'))->firstWhere('id', $dto['data_id']);

        if (!$data) {
            throw new BadRequestHttpException('Data not found');
        }

        $amount = $data['amount'];

        $billTransaction = $this->createBillTransaction(BaseBillDto::fromArray([
                  'amount' => $amount,
                  'type' => BillEnum::DATA,
                  'payload' => $dto,
                  'description' => 'Data purchase',
              ]));

        return $this->provider->billPurchase($billTransaction, fn () => $this->provider->purchaseData($billTransaction));
    }
}
