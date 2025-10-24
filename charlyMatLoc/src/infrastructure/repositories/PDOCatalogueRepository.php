<?php

namespace charlyMatLoc\src\infrastructure\repositories;

use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use charlyMatLoc\src\application_core\domain\entities\Outils;
use PDO;
use PDOException;

class PDOCatalogueRepository implements CatalogueRepositoryInterface{
    private \PDO $pdo;

    public function __construct(\PDO $pdo){
        $this->pdo = $pdo;
    }

    public function listerOutils(?string $userId = null): array{
        $stmt = $this->pdo->prepare('SELECT o.*, (SELECT url FROM images_outils WHERE outil_id = o.id LIMIT 1) AS image_url FROM outils o');
        $stmt->execute();

        $outils = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $stock_panier = 0;
            if ($userId) {
                $sqlPanier = 'SELECT COUNT(*) FROM panier WHERE outil_id = :outil_id AND user_id = :user_id';
                $stmtPanier = $this->pdo->prepare($sqlPanier);
                $stmtPanier->bindParam(':outil_id', $row['id'], \PDO::PARAM_INT);
                $stmtPanier->bindParam(':user_id', $userId, \PDO::PARAM_STR);
                $stmtPanier->execute();
                $stock_panier = (int)$stmtPanier->fetchColumn();
            }
            $row['stock_affiche'] = $row['nb_exemplaires'] - $stock_panier;
            $outils[] = $row;
        }
        return $outils;
    }

    /**
     * Récupère les détails d’un outil spécifique
     */
    public function detailsOutil(int|string $id): ?Outils{
        try {
            $sql = "
                SELECT o.id, o.nom, o.description, o.tarif, c.nom AS categorie, io.url AS image_url
                FROM outils o
                JOIN categories c ON o.categorie_id = c.id
                JOIN images_outils io ON o.id = io.outil_id
                WHERE o.id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            $images = $this->getImagesByOutilId($id);

            // On crée une entité Outil
            $outil = new Outils(
                (int)$row['id'],
                $row['nom'],
                $row['description'],
                (float)$row['tarif'],
                $row['categorie'],
                $row['image_url'],
                $images
            );

            return $outil;

        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Récupère les images associées à un outil
     */
    private function getImagesByOutilId(int $id): array{
        try {
            $sql = "
                SELECT id, outil_id, url, description
                FROM images_outils
                WHERE outil_id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }
}