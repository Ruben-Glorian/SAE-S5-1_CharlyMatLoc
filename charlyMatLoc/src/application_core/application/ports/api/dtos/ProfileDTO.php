<?php
declare(strict_types=1);

namespace charlyMatLoc\src\application_core\application\ports\api\dtos;

class ProfileDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $email
    ) {
    }
}