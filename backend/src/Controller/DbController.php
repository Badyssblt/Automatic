<?php

namespace App\Controller;

use App\Service\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DbController extends AbstractController
{
    #[Route('/api/user/export', name: 'app_user_export', methods: ['GET'])]
    public function exportSql(DatabaseService $databaseService, Request $request): Response
    {
        $type = $request->query->get('type');
        $sql = $request->get('sql');
        if($type === "csv"){
            return $databaseService->exportDatabaseToCsv($sql);
        }else if($type === "json"){
            return $databaseService->exportDatabaseToJson($sql);
        }

        return $this->json(['message' => 'Vous devez spécifier un type de requête'], Response::HTTP_BAD_REQUEST);

    }

    #[Route('/api/user/databases', name: 'app_user_databases', methods: ['GET'])]
    public function getDatabases(DatabaseService $databaseService, Request $request): Response
    {
        $databases = $databaseService->getAllDatabases();

        return $this->json($databases, Response::HTTP_OK);

    }

    #[Route('/api/user/tables', name: 'app_user_tables', methods: ['GET'])]
    public function getTables(Request $request, DatabaseService $databaseService): Response
    {
        $database = $request->query->get('database');

        $tables = $databaseService->getTablesFromDatabase($database);

        return $this->json($tables, Response::HTTP_OK);
    }



    #[Route('/api/user/import', name: 'app_user_import', methods: ['POST'])]
    public function importExcel(Request $request, DatabaseService $databaseService): Response
    {
        // Récupérer le fichier téléchargé
        $file = $request->files->get('file');
        $table = $request->get('table');

        if (!$file) {
            return new $this->json(['error' => 'Aucun fichier fourni.'], Response::HTTP_BAD_REQUEST);
        }

        return $databaseService->importFromExcel($file, $table);
    }


}
