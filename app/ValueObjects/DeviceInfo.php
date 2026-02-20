<?php

namespace App\ValueObjects;

class DeviceInfo
{
    public function __construct(
        public string $identifier,
        public ?string $name = null,
        public ?string $platform = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}

    public static function fromArray(array $raw): self
    {
        $identifier = isset($raw['device_identifier']) ? (string) $raw['device_identifier'] : '';

        if (trim($identifier) === '') {
            throw new \InvalidArgumentException('device_identifier is required');
        }

        return new self(
            identifier: $identifier,
            name: isset($raw['device_name']) ? (string) $raw['device_name'] : null,
            platform: isset($raw['platform']) ? (string) $raw['platform'] : null,
            ipAddress: isset($raw['ip_address']) ? (string) $raw['ip_address'] : null,
            userAgent: isset($raw['user_agent']) ? (string) $raw['user_agent'] : null,
        );
    }
}
