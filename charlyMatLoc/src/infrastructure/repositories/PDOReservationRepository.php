<?php
namespace charlyMatLoc\src\infrastructure\repositories;

use PDO;
use charlyMatLoc\src\application_core\application\ports\spi\ReservationRepositoryInterface;

class PDOReservationRepository implements ReservationRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function listerReservations(string $userId): array
    {
        if (empty($userId)) {
            return ['items' => [], 'total' => 0.0];
        }

        $sql = "
            SELECT r.id, r.user_id, r.outil_id, r.date_location, r.date_reservation,
                   o.nom AS outil_nom,
                   io.url AS image_url
            FROM reservations r
            LEFT JOIN outils o ON o.id = r.outil_id
            LEFT JOIN images_outils io ON io.outil_id = o.id
            WHERE r.user_id = :user_id
            ORDER BY r.date_reservation DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => isset($row['id']) ? (int)$row['id'] : 0,
                'user_id' => isset($row['user_id']) ? (string)$row['user_id'] : '',
                'outil_id' => isset($row['outil_id']) ? (string)$row['outil_id'] : '',
                'date_location' => $row['date_location'] ?? null,
                'date_reservation' => $row['date_reservation'] ?? null,
                'outil_nom' => $row['outil_nom'] ?? null,
                'image_url' => $row['image_url'] ?? null,
            ];
        }

        return $items;
    }
    public function ajouterOutil(int $idOutil, string $date, ?string $userId = null): void
    {
        if ($userId === null) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $userId = $_SESSION['user_id'] ?? null;
        }

        if (!$userId) {
            throw new \RuntimeException('Utilisateur non connecté');
        }

        $sql = "INSERT INTO reservations (user_id, outil_id, date_location) VALUES (:user_id, :outil_id, :date_location)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'outil_id' => $idOutil,
            'date_location' => $date,
        ]);
    }
}
