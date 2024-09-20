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
        $response->headers->set('Content-Disposition', 'attachment; filename="export.xlsx"');

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
            // Charger le fichier Excel
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            // Récupérer les données du fichier Excel
            $rows = $sheet->toArray(null, true, true, true);

            // La première ligne doit contenir les en-têtes de colonnes
            $headers = array_shift($rows);

            if (empty($headers)) {
                throw new \Exception('Le fichier Excel ne contient pas d\'en-têtes.');
            }

            // Préparation des colonnes à ignorer ou ajuster (par ex. 'id' si auto-incrémenté)
            $columns = [];
            foreach ($headers as $header) {
                if ($header !== 'id') {
                    $columns[] = "`$header`";
                }
            }

            // Vérifier si les colonnes sont valides
            if (empty($columns)) {
                throw new \Exception('Aucune colonne valide trouvée dans le fichier.');
            }

            $columnsSql = implode(',', $columns);
            $insertSql = "INSERT INTO `$tableName` ($columnsSql) VALUES ";

            $values = [];
            foreach ($rows as $row) {
                // Filtrer les colonnes à insérer (ignorer 'id')
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

            // Ajouter "ON DUPLICATE KEY UPDATE" pour mettre à jour les doublons
            $updateSql = implode(',', array_map(fn($col) => "$col=VALUES($col)", $columns));
            $insertSql .= " ON DUPLICATE KEY UPDATE $updateSql";

            // Exécuter la requête d'insertion
            $connection->executeStatement($insertSql);

            return new JsonResponse(['message' => 'Données importées avec succès !'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'importation : ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
