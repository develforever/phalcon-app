<?php
namespace AppDomain\Application\User\Command;


final class ChangeUserEmailCommand
{
    public function __construct(
        public string $userId,
        public string $email
    ) {}
}
