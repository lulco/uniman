<?php

namespace Adminerng\Core\Credentials;

use Nette\Http\Session;

class SessionStorageCredentials implements CredentialsStorageInterface
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setCredentials($driver, array $credentials)
    {
        $section = $this->session->getSection('adminerng');
        $section->$driver = base64_encode(json_encode($credentials));
    }

    public function getCredentials($driver)
    {
        $section = $this->session->getSection('adminerng');
        $settings = $section->$driver;
        if (!$settings) {
            return [];
        }
        $credentials = json_decode(base64_decode($settings), true);
        if (!$credentials) {
            return [];
        }
        return $credentials;
    }
}
