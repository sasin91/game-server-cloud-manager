<?php

namespace App\Scripts\AzerothCore;

use App\Contracts\ShellScript;

class CreateGameAccountThroughTelnet implements ShellScript
{
    /**
     * The user that the script should be run as.
     *
     * @var string
     */
    public $sshAs = 'acore';

    /**
     * The telnet server IP after establishing the SSH connection.
     *
     * @var string
     */
    public $address = '127.0.0.1';

    /**
     * The telnet server IP after establishing the SSH connection.
     *
     * @var integer
     */
    public $port = 3443;

    /**
     * The ACore admin user name
     *
     * @var string
     */
    public $adminName = 'admin';

    /**
     * The ACore admin user password.
     *
     * @var string
     */
    public $adminPassword = 'password';

    /**
     * The account name
     *
     * @var string
     */
    public $name;

    /**
     * The password
     *
     * @var string
     */
    public $password;

    /**
     * Create a new script instance.
     *
     * @param string $name
     * @param string $password
     * @param string $publicKey
     */
    public function __construct($name, $password)
    {
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * Get the contents of the script.
     *
     * @return string
     */
    public function render()
    {
        return view('scripts.AzerothCore.createAccountThroughTelnet', [
            'adminName' => $this->adminName,
            'adminPassword' => $this->adminPassword,
            'address' => $this->address,
            'port' => $this->port,
            'command' => "ACCOUNT CREATE {$this->name} {$this->password} {$this->password}"
        ])->render();
    }
}
