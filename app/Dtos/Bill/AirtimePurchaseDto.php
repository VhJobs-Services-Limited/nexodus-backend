<?php

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AirtimePurchaseDto extends BaseDto
{
    public string|int $amount;
    public string $provider_id;
    public string $phone_number;

    protected function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:50', 'max:200000'],
            'provider_id' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'size:11'],
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
