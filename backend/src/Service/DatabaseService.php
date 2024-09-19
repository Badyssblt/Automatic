<?php

// src/Service/DatabaseExportService.php

namespace App\Service;

use App\Entity\User;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseService
{
    private EntityManagerInterface $manager;
    private Security $security;

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    /**
     * Crée une connexion à la base de données de l'utilisateur.
     *
     * @return \Doctrine\DBAL\Connection
     * @throws \Exception
     */
    private function createUserDatabaseConnection(): \Doctrine\DBAL\Connection
    {
        /**
         * @var User
         */
        $user = $this->security->getUser();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $dbUrl = $user->getDb(); // URL de la base de données utilisateur

        // Créer une nouvelle connexion
        $connectionParams = ['url' => $dbUrl];
        return DriverManager::getConnection($connectionParams);
    }

    /**
     * Exporte les données de la base de données de l'utilisateur au format CSV.
     *
     * @return StreamedResponse
     */
    public function exportDatabaseToCsv(string $sql): StreamedResponse
    {
        $connection = $this->createUserDatabaseConnection();


        // Créer une réponse pour le téléchargement
        $response = new StreamedResponse(function() use ($connection, $sql) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $stmt = $connection->executeQuery($sql);

            // Obtenir les résultats
            $columns = $stmt->fetchAllAssociative();

            if (count($columns) > 0) {
                // Écrire les en-têtes
                $headers = array_keys($columns[0]);
                $sheet->fromArray($headers, null, 'A1');

                // Écrire les lignes de données
                $rows = [];
                foreach ($columns as $row) {
                    $rows[] = array_values($row);
                }
                $sheet->fromArray($rows, null, 'A2');
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="user_data_export.xlsx"');

        return $response;
    }

    /**
     * Exporte les données de la base de données de l'utilisateur au format SQL.
     *
     * @return StreamedResponse
     */
    public function exportDatabaseToSql(): StreamedResponse
    {
        // TODO: Implémentez l'exportation au format SQL si nécessaire.
        throw new \Exception('SQL export not implemented yet.');
    }
}
