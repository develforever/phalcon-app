<?php
namespace AppDomain\Presentation\Api;

use Phalcon\Mvc\Controller;
use AppDomain\Application\User\Command\RegisterUserCommand;
use AppDomain\Application\User\Command\ChangeUserEmailCommand;
use AppDomain\Application\User\Handler\ChangeUserEmailHandler;
use AppDomain\Application\User\Handler\RegisterUserHandler;
use AppDomain\Application\User\Query\UserReadModel;

class UsersController extends Controller
{
    public function createAction()
    {
        $data = $this->request->getJsonRawBody(true);

        // walidacja pominięta dla skrótu
        $command = new RegisterUserCommand(
            $data['name'] ?? '',
            $data['email'] ?? ''
        );

        /** @var RegisterUserHandler $handler */
        $handler = $this->di->get(RegisterUserHandler::class);
        $userId = $handler->handle($command);

        return $this->response
            ->setStatusCode(201, 'Created')
            ->setJsonContent([
                'id' => $userId->toString(),
            ]);
    }

    public function listAction()
    {
        /** @var UserReadModel $readModel */
        $readModel = $this->di->get(UserReadModel::class);

        $users = $readModel->listAll();

        return $this->response->setJsonContent($users);
    }

    public function viewAction(string $id)
    {
        /** @var UserReadModel $readModel */
        $readModel = $this->di->get(UserReadModel::class);

        $user = $readModel->getById($id);

        if (!$user) {
            return $this->response
                ->setStatusCode(404, 'Not Found')
                ->setJsonContent(['error' => 'User not found']);
        }

        return $this->response->setJsonContent($user);
    }

    public function changeEmailAction(string $id)
    {
        $data = $this->request->getJsonRawBody(true);

        $command = new ChangeUserEmailCommand(
            $id,
            $data['email'] ?? ''
        );

        /** @var ChangeUserEmailHandler $handler */
        $handler = $this->di->get(ChangeUserEmailHandler::class);
        $handler->handle($command);

        return $this->response->setJsonContent(['status' => 'ok']);
    }
}
