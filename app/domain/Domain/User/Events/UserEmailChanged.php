<?php
namespace AppDomain\Domain\User\Events;

use AppDomain\Domain\User\Email;
use AppDomain\Domain\User\UserId;

final class UserEmailChanged
{
    public function __construct(
        public readonly UserId $userId,
        public readonly Email $newEmail
    ) {}
}
