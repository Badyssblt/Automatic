<?php

namespace App\Controller;

use App\Repository\MailTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MailController extends AbstractController
{
    #[Route('/api/mails', name: 'app_mail')]
    public function index(MailTemplateRepository $mailTemplateRepository): Response
    {
        $mails = $mailTemplateRepository->findBy(['user' => $this->getUser()], ['name' => 'ASC']);

        return $this->json($mails, Response::HTTP_OK, [], ['groups' => ['collection:mail']]);
    }
}
