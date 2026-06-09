<?php

declare(strict_types=1);

namespace App\ValueObjects;

class CheckoutCustomerData
{
    public function __construct(
        public ?string $fullName = null,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fullName: self::normalize($data['buyer_name'] ?? $data['full_name'] ?? null),
            email: self::normalize($data['buyer_email'] ?? $data['email'] ?? null),
            phone: self::normalize($data['buyer_phone'] ?? $data['phone'] ?? null),
        );
    }

    private static function normalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
