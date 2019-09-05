<?php

namespace App;

class SecureShellCommand
{
    public $command;

    public $user = 'root';

    public $address = '127.0.0.1';

    public $port = 22;

    public $key;

    public function __construct(string $command, string $sshKey = null)
    {
        $this->command = $command;
        $this->key = $sshKey;
    }

    public function __toString()
    {
        return implode(' ', [
            'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no',
            '-i '.$this->key,
            '-p '.$this->port,
            $this->user.'@'.$this->address,
            $this->command
        ]);
    }
}
