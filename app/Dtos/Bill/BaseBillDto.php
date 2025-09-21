<?php

declare(strict_types=1);

namespace App\Dtos\Bill;

use App\Dtos\BaseDto;
use App\Enums\BillEnum;
use Illuminate\Validation\Rule;

final class BaseBillDto extends BaseDto
{
    public float|int $amount;
    public string $description;
    public string $type;
    public array $payload;

    protected function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['sometimes', 'string'],
            'type' => ['required', 'string', Rule::in(BillEnum::values())],
            'payload' => ['required', 'array'],
        ];
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
