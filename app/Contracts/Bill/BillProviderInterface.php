<?php

namespace App\Contracts\Bill;

use Illuminate\Support\Collection;

interface BillProviderInterface
{
    public static function getProviderName(): string;
    public function getWalletBalance(): mixed;
    public function getAirtimeProviders(): Collection;
    public function getDataProviders(): Collection;
    public function getWifiProviders(): Collection;
    public function getCableProviders(): Collection;
    public function getElectricityProviders(): Collection;
    public function getBettingProviders(): Collection;
}
