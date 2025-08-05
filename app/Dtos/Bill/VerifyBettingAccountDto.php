<?php

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VerifyBettingAccountDto extends BaseDto
{
    public string $provider_id;
    public string $account_id;

    protected function rules(): array
    {
        return [
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
