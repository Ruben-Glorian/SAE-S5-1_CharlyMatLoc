<?php

namespace charlyMatLoc\src\infrastructure\repositories;

use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use charlyMatLoc\src\application_core\domain\entities\Outils;
use PDO;
use PDOException;

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

    public function getAllOutils(): array
    {
        try {
            $sql = "
                SELECT o.id, o.nom, o.tarif, c.nom AS categorie,
                       (SELECT url FROM images_outils WHERE outil_id = o.id LIMIT 1) AS image_url
                FROM outils o
                JOIN categories c ON o.categorie_id = c.id
                ORDER BY o.nom
            ";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // gestion d'erreur à améliorer si besoin
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Récupère les détails d’un outil spécifique
     */
    public function detailsOutil(int|string $id): ?Outils
    {
        try {
            $sql = "
                SELECT o.id, o.nom, o.description, o.tarif, c.nom AS categorie
                FROM outils o
                JOIN categories c ON o.categorie_id = c.id
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
    private function getImagesByOutilId(int $id): array
    {
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

    /**
     * Récupère toutes les catégories
     */
    public function getCategories(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT id, nom, description FROM categories ORDER BY nom");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


}