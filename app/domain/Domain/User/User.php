<?php
declare(strict_types=1);
namespace AppDomain\Domain\User;

use AppDomain\Domain\User\Events\UserEmailChanged;
use AppDomain\Domain\User\Events\UserRegistered;

final class User
{
    private UserId $id;
    private string $name;
    private Email $email;

    /** @var array<object> */
    private array $recordedEvents = [];

    private function __construct() {}

    public static function register(UserId $id, string $name, Email $email): self
    {
        $self = new self();
        $self->apply(new UserRegistered($id, $name, $email));
        return $self;
    }

    public function changeEmail(Email $newEmail): void
    {
        if ($this->email->toString() === $newEmail->toString()) {
            return;
        }

        $this->apply(new UserEmailChanged($this->id, $newEmail));
    }

    private function apply(object $event): void
    {
        $this->when($event);
        $this->recordedEvents[] = $event;
    }

    // odtwarzanie z historii
    public static function reconstitute(UserId $id, array $events): self
    {
        $self = new self();
        foreach ($events as $event) {
            $self->when($event);
        }
        // ustaw id na końcu (lub z pierwszego eventu)
        $self->id = $id;
        return $self;
    }

    private function when(object $event): void
    {
        switch (true) {
            case $event instanceof UserRegistered:
                $this->id    = $event->userId;
                $this->name  = $event->name;
                $this->email = $event->email;
                break;

            case $event instanceof UserEmailChanged:
                $this->email = $event->newEmail;
                break;
        }
    }

    /** @return array<object> */
    public function pullRecordedEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }

    // gettery do odczytu
    public function id(): UserId   { return $this->id; }
    public function name(): string { return $this->name; }
    public function email(): Email { return $this->email; }
}
