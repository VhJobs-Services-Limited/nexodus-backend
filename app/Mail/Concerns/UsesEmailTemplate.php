<?php

declare(strict_types=1);

namespace App\Mail\Concerns;

trait UsesEmailTemplate
{
    /**
     * Set the SendPulse template ID.
     */
    public function template(int|string $templateId): self
    {
        $this->withSymfonyMessage(function ($message) use ($templateId) {
            $message->getHeaders()->add('X-Template-Id', (string) $templateId);
        });

        return $this;
    }

    /**
     * Set the template variables.
     *
     * @param  array<string, mixed>  $variables
     */
    public function variables(array $variables): self
    {
        $this->withSymfonyMessage(function ($message) use ($variables) {
            $message->getHeaders()->add('X-Variables', json_encode($variables));
        });

        return $this;
    }
}
