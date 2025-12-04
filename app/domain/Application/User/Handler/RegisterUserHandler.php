<?php
namespace AppDomain\Application\User\Handler;

use AppDomain\Application\User\Command\RegisterUserCommand;
use AppDomain\Domain\User\Email;
use AppDomain\Domain\User\User;
use AppDomain\Domain\User\UserId;
use AppDomain\Domain\User\UserRepository;

final class RegisterUserHandler
{
    public function __construct(
        private UserRepository $users
    ) {}

    public function handle(RegisterUserCommand $cmd): UserId
    {
        $id = UserId::generate();
        $user = User::register($id, $cmd->name, Email::fromString($cmd->email));
        $this->users->save($user);

        return $id;
    }
}

