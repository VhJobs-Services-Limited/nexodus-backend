<?php

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VerifyMetreNumberDto extends BaseDto
{
    public string $provider_id;
    public string $metre_number;

    protected function rules(): array
    {
        return [
            'provider_id' => ['required', 'string'],
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
