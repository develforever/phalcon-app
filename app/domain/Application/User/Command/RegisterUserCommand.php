<?php
namespace AppDomain\Application\User\Command;

final class RegisterUserCommand
{
    public function __construct(
        public string $name,
        public string $email
    ) {}
}

