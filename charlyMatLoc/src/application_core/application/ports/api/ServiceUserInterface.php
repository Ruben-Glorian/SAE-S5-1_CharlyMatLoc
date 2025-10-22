<?php

namespace charlyMatLoc\src\application_core\application\ports\api;

use charlyMatLoc\src\application_core\application\ports\api\dtos\CredentialsDTO;
use charlyMatLoc\src\application_core\application\ports\api\dtos\ProfileDTO;

interface ServiceUserInterface{

    public function register(CredentialsDTO $credentials, int $role): ProfileDTO;
    public function byCredentials(CredentialsDTO $credentials): ?ProfileDTO;
}