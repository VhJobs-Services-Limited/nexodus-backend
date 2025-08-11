<?php

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WifiPurchaseDto extends BaseDto
{
    public string $provider_id;
    public string $device_id;
    public string $plan_id;

    protected function rules(): array
    {
        return [
            'provider_id' => ['required', 'string'],
            'device_id' => ['required', 'string'],
            'plan_id' => ['required', 'string'],
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
