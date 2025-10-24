<?php

namespace charlyMatLoc\src\infrastructure\repositories;

use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;

class PDOPanierRepository implements PanierRepositoryInterface{
    private \PDO $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }

    public function listerPanier(string $userId): array{
        $sql = "SELECT p.id, p.outil_id, p.date_location, p.date_ajout, o.nom, o.tarif, i.url AS image_url
                FROM panier p
                JOIN outils o ON p.outil_id = o.id
                LEFT JOIN images_outils i ON o.id = i.outil_id
                WHERE p.user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->execute();
        $items = [];
        $total = 0.0;
        //parcours les résultats et construit le tableau des items
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $items[] = [
                'id' => $row['id'],
                'outil_id' => $row['outil_id'],
                'nom' => $row['nom'],
                'tarif' => $row['tarif'],
                'date_location' => $row['date_location'],
                'date_ajout' => $row['date_ajout'],
                'image_url' => $row['image_url']
            ];
            $total += (float)$row['tarif'];
        }
        //retourne le panier et le total
        return [
            'items' => $items,
            'total' => $total
        ];
    }

    public function ajouterOutil(int $idOutil, string $date, string $userId): void{
        $sql = "INSERT INTO panier (outil_id, user_id, date_location, date_ajout) VALUES (:outil_id, :user_id, :date_location, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':outil_id', $idOutil, \PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->bindParam(':date_location', $date, \PDO::PARAM_STR);
        $stmt->execute();
    }

    //cas des doublons
    public function verifDoublons(int $idOutil, string $date, string $userId): bool {
        $panier = $this->listerPanier($userId);
        foreach ($panier['items'] as $item) {
            if ($item['outil_id'] == $idOutil && $item['date_location'] == $date) {
                return true;
            }
        }
        return false;
    }

    //verif conflit de période
    public function verifConflitPeriode(string $userId, string $dateDebut, string $dateFin): bool {
        $sql = "SELECT COUNT(*) FROM panier WHERE user_id = :user_id AND date_location BETWEEN :debut AND :fin";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_STR);
        $stmt->bindParam(':debut', $dateDebut, \PDO::PARAM_STR);
        $stmt->bindParam(':fin', $dateFin, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    //retourne l'objet pdo
    public function getPDO(): \PDO {
        return $this->pdo;
    }
}