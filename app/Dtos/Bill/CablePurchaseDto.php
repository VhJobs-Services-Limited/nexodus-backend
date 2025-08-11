<?php

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CablePurchaseDto extends BaseDto
{
    public string $provider_id;
    public string $smart_card_number;
    public string $package_id;
    public string $phone_number;

    protected function rules(): array
    {
        return [
            'provider_id' => ['required', 'string'],
            'smart_card_number' => ['required', 'string'],
            'package_id' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
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
