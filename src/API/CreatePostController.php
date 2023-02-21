<?php

declare(strict_types=1);

namespace App\API;

use App\Post\Application\Command\CreatePostCommand;
use App\Shared\Domain\Bus\CommandBus;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Forms;

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
        $validator = Validation::createValidator();
        $constraint = new Assert\Collection([
            'title' => [
                new Assert\Regex(pattern: "#^(?!Qwerty*$).*#i", message: "Value must not start with 'Qwerty'"),
                new Assert\Length(['max' => 255]),
                new Assert\NotBlank(),
            ],
            'summary' => [new Assert\Length(['max' => 255]), new Assert\NotBlank()],
            'description' => [],
        ]);
        $violations = $validator->validate($payload, $constraint);
        if ($violations->count()) {
            $errorMessages = [];
            for ($i = 0; $i < $violations->count(); $i++) {
                $violation = $violations->get($i);
                $errorMessages[] = array($violation->getPropertyPath() => $violation->getMessage());
            }
            return new JsonResponse(
                [
                    'error' => $errorMessages,
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

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
