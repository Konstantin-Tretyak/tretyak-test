<?php

declare(strict_types=1);

namespace App\Post\Application\Query;

use App\Post\Domain\Post;
use App\Post\Domain\PostRepository;
use App\Post\Response\PostResponse;
use App\Shared\Domain\Bus\QueryHandler;
use App\Shared\Domain\Bus\Response;
use Symfony\Component\Uid\Uuid;

class ShowPostQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly PostRepository $repository
    ) {
    }

    public function __invoke(ShowPostQuery $query): Response|null
    {
        $id = Uuid::fromString($query->id);

        $post = $this->repository->find($id);

        if (!$post) {
            return null;
        }

        return new PostResponse($post);
    }
}
