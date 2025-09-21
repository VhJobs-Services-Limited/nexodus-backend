<?php

declare(strict_types=1);

namespace App\Dtos\Crypto;

use App\Dtos\BaseDto;
use App\Enums\PaymentMethodEnum;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SellCryptoDto extends BaseDto
{
    public string|int $amount;
    public string $currency;
    public ?string $payment_method;
    public ?int $bank_id;

    protected function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string'],
            'payment_method' => ['required', 'string', Rule::in(PaymentMethodEnum::values())],
            'bank_id' => [Rule::requiredIf(fn () => request()->get('payment_method') === PaymentMethodEnum::Bank->value), 'integer', Rule::exists('banks', 'id')->where('user_id', request()->user()->id)],
        ];
    }

    protected function failedValidation(): void
    {
        throw new BadRequestHttpException($this->validator->errors()->first());
    }

    protected function defaults(): array
    {
        return [
            'bank_id' => null,
            'payment_method' => null,
        ];
    }

    protected function casts(): array
    {
        return [];
    }
}
