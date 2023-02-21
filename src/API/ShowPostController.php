<?php

declare(strict_types=1);

namespace App\API;

use App\Post\Application\Query\ShowPostQuery;
use App\Shared\Domain\Bus\QueryBus;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/posts/{id}', methods: ['GET'])]
class ShowPostController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $showPostQuery = new ShowPostQuery($id);
        $post = $this->queryBus->ask(query: $showPostQuery);
        if (!$post) {
            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($post->get(), Response::HTTP_OK);
    }
}
