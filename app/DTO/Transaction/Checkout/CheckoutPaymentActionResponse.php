<?php

declare(strict_types=1);

namespace App\DTO\Transaction\Checkout;

class CheckoutPaymentActionResponse
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public string $type,
        public string $url,
        public ?array $metadata = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'url' => $this->url,
            'metadata' => $this->metadata,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
