<?php

namespace App\ValueObjects;

class QueryOption
{
    public function __construct(
        public ?int $page = null,
        public int $perPage = 15,
        /** @var string[] */
        public array $with = [],
    ) {}

    /* =========================
     |  Getters
     ========================= */

    public function getPage(): ?int
    {
        if ($this->page === null) {
            throw new \TypeError('QueryOption page is not set.');
        }

        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return string[]
     */
    public function getWith(): array
    {
        return $this->with;
    }

    /* =========================
     |  Setters (chainable)
     ========================= */

    public function setPage(?int $page = null): self
    {
        if ($page === null) {
            return $this;
        }
        $this->page = max(1, $page);

        return $this;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = max(1, $perPage);

        return $this;
    }

    /**
     * @param  string[]  $with
     */
    public function setWith(array $with): self
    {
        // lọc chỉ string
        $this->with = array_values(array_filter($with, 'is_string'));

        return $this;
    }

    /* =========================
     |  Factory
     ========================= */

    public static function fromArray(array $raw): self
    {
        $dto = new self;

        if (isset($raw['page'])) {
            $dto->setPage((int) $raw['page']);
        }

        if (isset($raw['perPage'])) {
            $dto->setPerPage((int) $raw['perPage']);
        }

        if (isset($raw['with'])) {
            // hỗ trợ cả "a,b,c" lẫn array
            $with = is_string($raw['with'])
                ? explode(',', $raw['with'])
                : (array) $raw['with'];

            $dto->setWith($with);
        }

        return $dto;
    }
}
