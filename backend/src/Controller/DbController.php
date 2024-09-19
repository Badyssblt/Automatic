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
}
