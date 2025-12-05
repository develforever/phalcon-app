<?php

declare(strict_types=1);

namespace Tests\Unit\app\Models;

use App\Models\Users;
use AppDomain\Domain\User\UserId;

class UsersTest extends \PHPUnit\Framework\TestCase
{

    public function testTestCase(): void
    {

        $users = new \App\Models\Users();
        $this->assertInstanceOf(\App\Models\Users::class, $users);
        $this->assertObjectHasProperty('id', $users);
        $this->assertObjectHasProperty('name', $users);
        $this->assertObjectHasProperty('email', $users);

        $valid = $users->validation();
        $this->assertFalse($valid);
        $users->assign(
            [
                'id'    => UserId::generate()->toString(),
                'name'  => 'John Doe',
                'email' => 'john.doe@example.com',
            ]
        );
        $valid = $users->validation();
        $this->assertTrue($valid);


        $result = $users->create();
        $this->assertTrue($result);

        $first = Users::findFirst(
            [
                'conditions' => 'email = :email:',
                'bind'       => [
                    'email' => 'john.doe@example.com',
                ],
            ]
        );  

        $this->assertInstanceOf(\App\Models\Users::class, $first);
        $this->assertEquals('John Doe', $first->name);
        $this->assertEquals('john.doe@example.com', $first->email);

    }
}
