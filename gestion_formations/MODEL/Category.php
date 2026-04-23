<?php

class Category
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $slug = '',
        public string $scope = 'formation',
        public string $description = ''
    ) {
    }
}
