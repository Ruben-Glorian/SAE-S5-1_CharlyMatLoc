<?php
namespace charlyMatLoc\src\application_core\application\ports\api;

use charlyMatLoc\src\application_core\domain\entities\Outils;

Interface ServiceCatalogueInterface{
    public function listerOutils(): array;
    public function detailsOutil(string $id): ?Outils;
}