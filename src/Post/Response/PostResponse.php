<?php


namespace App\Post\Response;

use App\Shared\Domain\Bus\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Post\Domain\Post;

class PostResponse implements Response
{
    public function __construct(
        private readonly Post $post
    ) {
    }

    public function get(): array
    {
        return [
            'post_id' => $this->post->getId(),
            'title' => $this->post->getTitle(),
            'summary' => $this->post->getSummary(),
        ];
    }
}
