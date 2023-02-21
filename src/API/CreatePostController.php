<?php

declare(strict_types=1);

namespace App\API;

use App\Post\Application\Command\CreatePostCommand;
use App\Shared\Domain\Bus\CommandBus;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route(path: '/posts', methods: ['POST'])]
class CreatePostController
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->toArray();

        $command = new CreatePostCommand(
            id: $payload['id'] ?? (string)Uuid::v4(),
            title: $payload['title'],
            summary: $payload['summary'],
            description: $payload['description'],
        );

        try {
            $this->commandBus->dispatch(
                command: $command,
            );
        } catch (Exception $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        return new JsonResponse(
            [
                'post_id' => $command->id,
            ],
            Response::HTTP_OK,
        );
    }
}
