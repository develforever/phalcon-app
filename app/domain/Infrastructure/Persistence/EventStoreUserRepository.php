<?php
namespace AppDomain\Infrastructure\Persistence;
use AppDomain\Domain\User\Events\UserEmailChanged;
use AppDomain\Domain\User\Events\UserRegistered;
use AppDomain\Domain\User\User;
use AppDomain\Domain\User\UserId;
use AppDomain\Domain\User\UserRepository;
use Phalcon\Db\Adapter\Pdo\AbstractPdo;

final class EventStoreUserRepository implements UserRepository
{
    public function __construct(
        private AbstractPdo $db
    ) {}

    public function get(UserId $id): ?User
    {
        $rows = $this->db->fetchAll(
            'SELECT * FROM events_store WHERE aggregate_type = :type AND aggregate_id = :id ORDER BY version ASC',
            \Phalcon\Db\Enum::FETCH_ASSOC,
            [
                'type' => 'user',
                'id'   => $id->toString()
            ]
        );

        if (!$rows) {
            return null;
        }

        $events = [];
        foreach ($rows as $row) {
            $payload = json_decode($row['payload'], true);

            switch ($row['event_type']) {
                case 'UserRegistered':
                    $events[] = new UserRegistered(
                        UserId::fromString($row['aggregate_id']),
                        $payload['name'],
                        \AppDomain\Domain\User\Email::fromString($payload['email'])
                    );
                    break;

                case 'UserEmailChanged':
                    $events[] = new UserEmailChanged(
                        UserId::fromString($row['aggregate_id']),
                        \AppDomain\Domain\User\Email::fromString($payload['new_email'])
                    );
                    break;
            }
        }

        return User::reconstitute($id, $events);
    }

    public function save(User $user): void
    {
        $events = $user->pullRecordedEvents();
        $version = $this->getCurrentVersion($user->id());

        foreach ($events as $event) {
            $version++;

            if ($event instanceof UserRegistered) {
                $eventType = 'UserRegistered';
                $payload = [
                    'name'  => $event->name,
                    'email' => $event->email->toString(),
                ];
            } elseif ($event instanceof UserEmailChanged) {
                $eventType = 'UserEmailChanged';
                $payload = [
                    'new_email' => $event->newEmail->toString(),
                ];
            } else {
                continue; // w prawdziwym kodzie: wyjątek
            }

            $this->db->insertAsDict('event_store', [
                'aggregate_id'   => $user->id()->toString(),
                'aggregate_type' => 'user',
                'version'        => $version,
                'event_type'     => $eventType,
                'payload'        => json_encode($payload),
                'occurred_at'    => date('Y-m-d H:i:s'),
            ]);

            // tutaj można jednocześnie aktualizować read model (tabela users)
            $this->projectToReadModel($event);
        }
    }

    private function getCurrentVersion(UserId $id): int
    {
        $row = $this->db->fetchOne(
            'SELECT MAX(version) AS v FROM event_store WHERE aggregate_type=:type AND aggregate_id=:id',
            \Phalcon\Db\Enum::FETCH_ASSOC,
            [
                'type' => 'user',
                'id'   => $id->toString()
            ]
        );

        return (int)($row['v'] ?? 0);
    }

    private function projectToReadModel(object $event): void
    {
        // bardzo prosta projekcja na tabelę `users`
        if ($event instanceof \AppDomain\Domain\User\Events\UserRegistered) {
            $this->db->insertAsDict('users', [
                'id'    => $event->userId->toString(),
                'name'  => $event->name,
                'email' => $event->email->toString(),
            ]);
        }

        if ($event instanceof \AppDomain\Domain\User\Events\UserEmailChanged) {
            $this->db->updateAsDict(
                'users',
                ['email' => $event->newEmail->toString()],
                'id = :id',
                ['id' => $event->userId->toString()]
            );
        }
    }
}
