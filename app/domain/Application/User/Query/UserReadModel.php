<?php
namespace AppDomain\Application\User\Query;

use App\Models\Users;

final class UserReadModel
{
    public function getById(string $id): ?array
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            return null;
        }

        return [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ];
    }

    public function listAll(): array
    {
        $users = Users::find();
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ];
        }

        return $result;
    }
}
