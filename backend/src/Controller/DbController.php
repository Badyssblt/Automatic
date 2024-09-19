<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DbController extends AbstractController
{
    #[Route('/db', name: 'app_db')]
    public function index(): Response
    {
        return $this->render('db/index.html.twig', [
            'controller_name' => 'DbController',
        ]);
    }
}
