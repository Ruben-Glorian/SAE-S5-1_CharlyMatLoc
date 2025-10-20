<?php
namespace charlyMatLoc\src\application_core\application\ports\api;

use charlyMatLoc\src\application_core\domain\entities\Outil;

Interface ServiceCatalogueInterface{
    public function listerOutils(): array;
    public function detailsOutil(string $id): ?Outil;
}