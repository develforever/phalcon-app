<?php

declare(strict_types=1);

namespace App\Controllers;

use AppDomain\Domain\User\UserId;
use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $id = UserId::generate()->toString();

        $this->view->setVar('userId', $id);
    }

}
