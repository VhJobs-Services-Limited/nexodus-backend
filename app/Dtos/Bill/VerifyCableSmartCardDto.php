<?php

declare(strict_types=1);

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class VerifyCableSmartCardDto extends BaseDto
{
    public string $provider_id;
    public string $smart_card_number;

    protected function rules(): array
    {
        return [
            'provider_id' => ['required', 'string'],
            'smart_card_number' => ['required', 'string'],
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
