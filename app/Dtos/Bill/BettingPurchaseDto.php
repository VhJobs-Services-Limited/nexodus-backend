<?php

declare(strict_types=1);

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class BettingPurchaseDto extends BaseDto
{
    public string|int $amount;
    public string $provider_id;
    public string $account_id;

    protected function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:100', 'max:50000'],
            'provider_id' => ['required', 'string'],
            'account_id' => ['required', 'string'],
        ];
    }

    protected function failedValidation(): void
    {
        throw new BadRequestHttpException($this->validator->errors()->first());
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}
