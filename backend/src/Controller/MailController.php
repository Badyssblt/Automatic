<?php

namespace App\Controller;

use App\Repository\MailTemplateRepository;
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


    #[Route('/api/send-mail/{mailKey}/{receiver}', name: 'app_send_mail', methods: ['POST'])]
    public function sendMail(string $mailKey, MailTemplateRepository $mailTemplateRepository, string $receiver, Request $request): Response
    {
        $user = $this->getUser();

        if(!$user)
        {
            return $this->json(['message' => 'Veuillez vous connectez'], Response::HTTP_UNAUTHORIZED);
        }

        $mail = $mailTemplateRepository->findOneBy(['user' => $user, 'token' => $mailKey]);

        if(!$mail){
            return $this->json(['message' => 'Une erreur est survenue, le mail est introuvable'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = $request->getPayload()->get('data');


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

    private function buildContent(string $content, mixed $data)
    {

        return str_replace('{{data}}', $data, $content);

    }
}
