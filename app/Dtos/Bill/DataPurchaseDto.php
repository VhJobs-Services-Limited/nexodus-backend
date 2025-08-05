<?php

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DataPurchaseDto extends BaseDto
{
    public string $provider_id;
    public string $phone_number;
    public string $data_id;

    protected function rules(): array
    {
        return [
            'provider_id' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'size:11'],
            'data_id' => ['required', 'string'],
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
