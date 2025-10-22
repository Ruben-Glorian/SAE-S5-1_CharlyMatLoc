<?php

namespace charlyMatLoc\src\api\providers;

use charlyMatLoc\src\application_core\application\ports\api\dtos\AuthDTO;
use charlyMatLoc\src\application_core\application\ports\api\dtos\CredentialsDTO;
use charlyMatLoc\src\application_core\application\ports\api\dtos\ProfileDTO;

interface AuthnProviderInterface {
    //public function register(CredentialsDTO $credentials, int $role): ProfileDTO;
    public function signin(CredentialsDTO $credentials): array;
    //public function refresh(Token $token): AuthDTO;
    //public function getSignedInUser(Token $token): ProfileDTO;
}
