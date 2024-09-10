<?php

namespace App\Controller;

use App\Repository\ApiKeyRepository;
use App\Repository\MailTemplateRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;


class MailController extends AbstractController
{
    #[Route('/api/mails', name: 'app_mail')]
    public function index(MailTemplateRepository $mailTemplateRepository): Response
    {
        $mails = $mailTemplateRepository->findBy(['user' => $this->getUser()], ['name' => 'ASC']);

        return $this->json($mails, Response::HTTP_OK, [], ['groups' => ['collection:mail']]);
    }


    #[Route('/api/send-mail/{mailKey}', name: 'app_send_mail', methods: ['POST'])]
    public function sendMail(string $mailKey, MailTemplateRepository $mailTemplateRepository, Request $request, ApiKeyRepository $apiKeyRepository): Response
    {
        $apiKey = $request->query->get('apiKey');

        $user = $apiKeyRepository->findOneBy(['token' => $apiKey])->getUser();


        if(!$user) {
            return $this->json(['message' => "Veuillez passer une clé d'API valide"], Response::HTTP_UNAUTHORIZED);
        }

        $mail = $mailTemplateRepository->findOneBy(['user' => $user, 'token' => $mailKey]);

        if(!$mail){
            return $this->json(['message' => 'Une erreur est survenue, le mail est introuvable'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if(!$mail->isDeploy()){
            return $this->json(['message' => 'Veuillez activer votre mail dans votre dashboard'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = $request->getPayload()->get('data');
        $receiver = $request->getPayload()->get('receiver');


        $smtp = $user->getSmtp();

        $transport = Transport::fromDsn($smtp);
        $mailer = new Mailer($transport);

        $css = file_get_contents('styles.css');


        $content = $this->buildContent($mail->getContent(), $data);


        $inliner = new CssToInlineStyles();
        $content = $inliner->convert($content, $css);


        $email = (new Email())
            ->from($user->getEmail())
            ->to($receiver)
            ->subject($mail->getSubject())
            ->html($content);

        $mailer->send($email);

        return $this->json(['message' => 'Le mail a bien été envoyé'], Response::HTTP_OK);
    }

    private function buildContent(string $content, mixed $data): string
    {

        return "
                <html>
                <head>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
                        
                    </style>
                </head>
                <body style='color: black; font-family: Inter, sans-serif;'>
                    " .str_replace('{{data}}', $data, $content) .
            "
                </body>
                </html>";

    }
}
