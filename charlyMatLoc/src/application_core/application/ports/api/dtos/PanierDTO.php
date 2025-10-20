<?php
namespace charlyMatLoc\src\application_core\application\ports\api\dtos;

use charlyMatLoc\src\application_core\domain\entities\Panier;

class PanierDTO{
    private Panier $newPanier;

    public function __construct(Panier $newPanier) {
        $this->newPanier = $newPanier;
    }

    public function getNewPanier(): Panier {
        return $this->newPanier;
    }
}