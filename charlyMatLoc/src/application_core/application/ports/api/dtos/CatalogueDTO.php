<?php
namespace charlyMatLoc\src\application_core\application\ports\api\dtos;

use charlyMatLoc\src\application_core\domain\entities\Outils;

class CatalogueDTO{
    private Outils $newOutil;

    public function __construct(Outils $newOutil) {
        $this->newOutil = $newOutil;
    }
}