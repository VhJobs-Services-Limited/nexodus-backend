<?php

declare(strict_types=1);

namespace App\Dtos;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

abstract class BaseDto extends ValidatedDTO
{
    protected function failedValidation(): void
    {
        throw new BadRequestHttpException($this->validator->errors()->first());
    }
}
