<?php

class Enrollment
{
    public function __construct(
        public ?int $id = null,
        public ?int $userId = null,
        public ?int $formationId = null,
        public ?string $enrolledAt = null,
        public int $progress = 0
    ) {
    }
}
