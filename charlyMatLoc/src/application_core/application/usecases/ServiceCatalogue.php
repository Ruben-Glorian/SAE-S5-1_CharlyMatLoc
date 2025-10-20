<?php

namespace charlyMatLoc\src\application_core\application\usecases;

use charlyMatLoc\src\application_core\application\ports\api\ServiceCatalogueInterface;
use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use charlyMatLoc\src\application_core\domain\entities\Outils;

class ServiceCatalogue implements ServiceCatalogueInterface {
    private CatalogueRepositoryInterface $catalogueRepository;

    public function __construct(CatalogueRepositoryInterface $catalogueRepository){
        $this->catalogueRepository = $catalogueRepository;
    }

    public function listerOutils(): array{
        return $this->catalogueRepository->listerOutils();
    }

    public function detailsOutil($id): ?Outils{
        return $this->catalogueRepository->detailsOutil($id);
    }
}