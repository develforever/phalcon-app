<?php
namespace AppDomain\Application\User\Handler;

use AppDomain\Application\User\Command\ChangeUserEmailCommand;
use AppDomain\Domain\User\Email;
use AppDomain\Domain\User\UserId;
use AppDomain\Domain\User\UserRepository;

final class ChangeUserEmailHandler
{
    public function __construct(
        private UserRepository $users
    ) {}

    public function handle(ChangeUserEmailCommand $cmd): void
    {
        $id   = UserId::fromString($cmd->userId);
        $user = $this->users->get($id);

        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $user->changeEmail(Email::fromString($cmd->email));
        $this->users->save($user);
    }
}
