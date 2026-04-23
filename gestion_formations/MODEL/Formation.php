<?php

class Formation
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
        public string $description = '',
        public ?int $categoryId = null,
        public string $level = 'Beginner',
        public float $price = 0.0,
        public int $durationHours = 0,
        public string $status = 'draft',
        public ?int $creatorId = null,
        public string $imageUrl = '',
        public string $tags = '',
        public ?string $createdAt = null
    ) {
    }
}
