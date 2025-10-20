<?php
namespace charlyMatLoc\src\infrastructure\repositories;

use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use charlyMatLoc\src\application_core\domain\entities\Outils;

class PDOCatalogueRepository implements CatalogueRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }

    public function listerOutils(): array{
        $stmt = $this->pdo->prepare('SELECT * FROM outils');
        $stmt->execute();

        $outils = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $outils[] = $row;
        }
        return $outils;
    }

    public function detailsOutil(string $id): ?Outils{
        return "bouh";
    }
}