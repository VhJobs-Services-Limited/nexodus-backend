<?php

namespace App\Contracts\Bill;

use App\Models\BillTransaction;
use Illuminate\Support\Collection;

interface BillProviderInterface
{
    public static function getProviderName(): string;
    public function getWalletBalance(): mixed;
    public function getAirtimeList(): Collection;
    public function getDataList(): Collection;
    public function getWifiList(): Collection;
    public function getCableList(): Collection;
    public function getElectricityList(): Collection;
    public function getBettingList(): Collection;
    public function billPurchase(BillTransaction $billTransaction, callable $responseFn): BillTransaction;
    public function purchaseAirtime(BillTransaction $billTransaction): Collection;
    public function purchaseData(BillTransaction $billTransaction): Collection;
    public function verifyBettingAccountId(string $bettingProvider, string $accountId): Collection;
    public function purchaseBetting(BillTransaction $billTransaction): Collection;
    public function verifyMetreNumber(string $electricityProvider, string $metreNumber): Collection;
    public function purchaseElectricity(BillTransaction $billTransaction): Collection;
}
