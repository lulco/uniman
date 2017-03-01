<?php

namespace UniMan\Core\Credentials;

interface CredentialsStorageInterface
{
    /**
     * @param array $credentials
     */
    public function setCredentials($driver, array $credentials);

    /**
     * @return array
     */
    public function getCredentials($driver);
}
