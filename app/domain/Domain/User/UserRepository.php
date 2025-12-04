<?php
namespace AppDomain\Domain\User;

interface UserRepository
{
    public function get(UserId $id): ?User;
    public function save(User $user): void;
}
