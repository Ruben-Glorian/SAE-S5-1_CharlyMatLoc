<?php
namespace charlyMatLoc\src\application_core\application\ports\api;

class AuthDTO{
    public function __construct(
        public string $accesToken,
        public string $refreshToken
    )
    {}
}