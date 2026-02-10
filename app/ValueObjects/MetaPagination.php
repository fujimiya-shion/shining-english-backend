<?php

namespace App\ValueObjects;

use Illuminate\Pagination\LengthAwarePaginator;

class MetaPagination
{
    public function __construct(
        public int $page,
        public int $perPage,
        public int $total,
        public int $pageCount,
    ) {}

    public static function fromTotalAndQueryOption(int $total, QueryOption $options): self
    {
        $pageCount = ceil($total / $options->getPerPage());

        return new self(
            $options->getPage(),
            $options->getPerPage(),
            $total,
            $pageCount,
        );
    }

    public static function fromLengthAwarePaginator(LengthAwarePaginator $paginator): self
    {
        $total = $paginator->total();
        $options = new QueryOption(
            page: $paginator->currentPage(),
            perPage: $paginator->perPage(),
        );

        return self::fromTotalAndQueryOption($total, $options);
    }

    public function toArray(bool $snakeCase = true): array
    {
        $page = $this->page;
        $perPage = $this->perPage;
        $total = $this->total;
        $pageCount = $this->pageCount;

        if (! $snakeCase) {
            return compact('page', 'perPage', 'total', 'pageCount');
        }

        return [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'page_count' => $pageCount,
        ];
    }
}
