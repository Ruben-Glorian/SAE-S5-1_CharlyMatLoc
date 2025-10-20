<?php
namespace charlyMatLoc\src\application_core\application\ports\spi\repositoryInterfaces;

interface AuthRepositoryInterface {
    public function findById (string $id): User;
}