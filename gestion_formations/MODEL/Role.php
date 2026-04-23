<?php

class Role
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $slug = '',
        public string $description = ''
    ) {
    }
}
