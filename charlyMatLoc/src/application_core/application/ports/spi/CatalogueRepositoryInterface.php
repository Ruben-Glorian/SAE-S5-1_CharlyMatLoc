<?php
namespace charlyMatLoc\src\application_core\application\ports\spi;

use charlyMatLoc\src\application_core\domain\entities\Outils;

Interface CatalogueRepositoryInterface{
    public function listerOutils(): array;
    public function detailsOutil(string $id): ?Outils;
}