<?php   

declare(strict_types=1);

class MainTask extends \Phalcon\Cli\Task
{
    public function mainAction(): void
    {
        echo "Hello! This is the main task.\n";
    }
}