<?php

// src/Service/DatabaseExportService.php

namespace App\Service;

use App\Entity\User;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Récupère toutes les bases de données accessibles par l'utilisateur.
     *
     * @return array
     * @throws \Exception
     */
    public function getAllDatabases(): array
    {
        $connection = $this->createUserDatabaseConnection();

        try {
            $sql = 'SHOW DATABASES';
            $stmt = $connection->executeQuery($sql);
            $databases = $stmt->fetchAllAssociative();

            return array_map(fn($db) => $db['Database'], $databases);

        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération des bases de données : ' . $e->getMessage());
        }
    }

    /**
     * Exporte les données de la base de données de l'utilisateur au format CSV.
     *
     * @return StreamedResponse
     */
    public function exportDatabaseToCsv(string $sql): StreamedResponse
    {
        $connection = $this->createUserDatabaseConnection();


        $response = new StreamedResponse(function() use ($connection, $sql) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $stmt = $connection->executeQuery($sql);

            $columns = $stmt->fetchAllAssociative();

            if (count($columns) > 0) {
                $headers = array_keys($columns[0]);
                $sheet->fromArray($headers, null, 'A1');

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
        $response->headers->set('Content-Disposition', 'attachment; filename="export.xlsx"');

        return $response;
    }

    /**
     * Exporte les données de la base de données de l'utilisateur au format JSON.
     *
     * @param string $sql La requête SQL à exécuter pour obtenir les données
     * @return StreamedResponse
     */
    public function exportDatabaseToJson(string $sql): StreamedResponse
    {
        $connection = $this->createUserDatabaseConnection();

        $response = new StreamedResponse(function() use ($connection, $sql) {
            $stmt = $connection->executeQuery($sql);
            $data = $stmt->fetchAllAssociative();

            echo json_encode($data);
        });

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.json"');

        return $response;
    }

    /**
* Importe les données depuis un fichier Excel dans la base de données de l'utilisateur.
 *
 * @param UploadedFile $file Le fichier Excel téléchargé par l'utilisateur
* @param string $tableName Le nom de la table dans laquelle insérer les données
* @return JsonResponse
*/
    public function importFromExcel(UploadedFile $file, string $tableName): JsonResponse
    {
        $connection = $this->createUserDatabaseConnection();

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            $rows = $sheet->toArray(null, true, true, true);

            $headers = array_shift($rows);

            if (empty($headers)) {
                throw new \Exception('Le fichier Excel ne contient pas d\'en-têtes.');
            }

            $columns = [];
            foreach ($headers as $header) {
                if ($header !== 'id') {
                    $columns[] = "`$header`";
                }
            }

            if (empty($columns)) {
                throw new \Exception('Aucune colonne valide trouvée dans le fichier.');
            }

            $columnsSql = implode(',', $columns);
            $insertSql = "INSERT INTO `$tableName` ($columnsSql) VALUES ";

            $values = [];
            foreach ($rows as $row) {
                $rowData = [];
                foreach ($headers as $key => $header) {
                    if ($header !== 'id') {
                        // Ajouter une validation sur certaines colonnes (ex: 'is_deploy')
                        $value = $row[$key];
                        if ($header === 'is_deploy' && ($value === '' || !is_numeric($value))) {
                            $value = 0; // Valeur par défaut si non valide
                        }
                        $rowData[] = $connection->quote($value);
                    }
                }
                $values[] = '(' . implode(',', $rowData) . ')';
            }

            $insertSql .= implode(',', $values);

            $updateSql = implode(',', array_map(fn($col) => "$col=VALUES($col)", $columns));
            $insertSql .= " ON DUPLICATE KEY UPDATE $updateSql";

            $connection->executeStatement($insertSql);

            return new JsonResponse(['message' => 'Données importées avec succès !'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'importation : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Récupère les noms des tables d'une base de données donnée.
     *
     * @param string $databaseName Le nom de la base de données
     * @return array
     * @throws \Exception
     */
    public function getTablesFromDatabase(string $databaseName): array
    {
        $connection = $this->createUserDatabaseConnection();

        try {
            // Utiliser la base de données spécifiée
            $connection->executeQuery("USE `$databaseName`");

            // Requête pour obtenir les tables
            $sql = 'SHOW TABLES';
            $stmt = $connection->executeQuery($sql);
            $tables = $stmt->fetchAllAssociative();

            // Le nom de la colonne retournée par 'SHOW TABLES' peut varier selon le SGBD.
            // Récupérons dynamiquement la première clé de chaque ligne pour garantir que ça marche dans tous les cas.
            $tableNames = array_map(fn($table) => reset($table), $tables);

            return $tableNames;

        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération des tables : ' . $e->getMessage());
        }
    }
}
