<?php

class User
{
    public function __construct(
        public ?int $id = null,
        public ?int $roleId = null,
        public string $firstName = '',
        public string $lastName = '',
        public string $email = '',
        public string $password = '',
        public string $headline = '',
        public string $bio = '',
        public string $avatarUrl = '',
        public string $status = 'active',
        public ?string $createdAt = null
    ) {
    }
}
