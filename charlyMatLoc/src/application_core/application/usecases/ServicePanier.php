<?php

namespace charlyMatLoc\src\application_core\application\usecases;

use charlyMatLoc\src\application_core\application\ports\api\ServicePanierInterface;
use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;

class ServicePanier implements ServicePanierInterface {
    private PanierRepositoryInterface $panierRepository;

    public function __construct(PanierRepositoryInterface $panierRepository){
        $this->panierRepository = $panierRepository;
    }

    public function listerPanier(): array{
        return $this->panierRepository->listerPanier();
    }
}