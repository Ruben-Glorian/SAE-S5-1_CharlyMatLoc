<?php

namespace charlyMatLoc\src\infrastructure\repositories;

use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;

class PDOPanierRepository implements PanierRepositoryInterface{
    private \PDO $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }

    public function listerPanier(): array{
        $sql = "SELECT p.id, p.outil_id, p.date_location, p.date_ajout, o.nom, o.tarif
                FROM panier p
                JOIN outils o ON p.outil_id = o.id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $items = [];
        $total = 0.0;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $items[] = [
                'id' => $row['id'],
                'outil_id' => $row['outil_id'],
                'nom' => $row['nom'],
                'tarif' => $row['tarif'],
                'date_location' => $row['date_location'],
                'date_ajout' => $row['date_ajout']
            ];
            $total += (float)$row['tarif'];
        }
        return [
            'items' => $items,
            'total' => $total
        ];
    }
}