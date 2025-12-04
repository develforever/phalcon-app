<?php
namespace AppDomain\Domain\User\Events;

use AppDomain\Domain\User\Email;
use AppDomain\Domain\User\UserId;

final class UserRegistered
{
    public function __construct(
        public readonly UserId $userId,
        public readonly string $name,
        public readonly Email $email
    ) {}
}

