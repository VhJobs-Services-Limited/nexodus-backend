<?php

declare(strict_types=1);

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ElectricityPurchaseDto extends BaseDto
{
    public string|int $amount;
    public string $provider_id;
    public string $metre_type;
    public string $phone_number;
    public string $metre_number;

    protected function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:100', 'max:50000'],
            'provider_id' => ['required', 'string'],
            'metre_type' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'size:11'],
            'metre_number' => ['required', 'string'],
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
