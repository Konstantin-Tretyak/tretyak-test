<?php

declare(strict_types=1);

namespace App\Post\Application\Query;

use App\Shared\Domain\Bus\Query;

class ShowPostQuery implements Query
{
    public function __construct(
        public readonly ?string $id
    ) {
    }
}
