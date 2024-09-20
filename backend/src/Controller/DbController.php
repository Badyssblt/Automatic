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
        $sql = $request->get('sql');
        return $databaseService->exportDatabaseToCsv($sql);
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
