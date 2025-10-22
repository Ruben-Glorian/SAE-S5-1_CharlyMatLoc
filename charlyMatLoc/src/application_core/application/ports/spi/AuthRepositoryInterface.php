<?php
namespace charlyMatLoc\src\application_core\application\ports\spi;

use charlyMatLoc\src\application_core\domain\entities\User;

interface AuthRepositoryInterface {
    public function findById (string $id): ?User;
}