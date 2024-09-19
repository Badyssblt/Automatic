<?php

namespace App\Controller;

use App\Entity\Mail;
use App\Repository\ApiKeyRepository;
use App\Repository\MailRepository;
use App\Repository\MailTemplateRepository;
use App\Repository\UserRepository;
use App\Service\EncryptionService;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class MailController extends AbstractController
{

    #[Route('/api/mails', name: 'app_mail')]
    public function index(MailRepository $mailTemplateRepository): Response
    {
        $mails = $mailTemplateRepository->findBy(['user' => $this->getUser()], ['name' => 'ASC']);

        return $this->json($mails, Response::HTTP_OK, [], ['groups' => ['collection:mail']]);
    }

    #[Route('/api/mails_template/{id}', name: 'app_mails_template_one')]
    public function getTemplate(Mail $mail): Response
    {
        if(!$mail->isTemplate()) return $this->json(['message' => "Ce mail n'est pas un template"], Response::HTTP_UNAUTHORIZED) ;
        return $this->json($mail, Response::HTTP_OK, [], ['groups' => ['item:mail']]);
    }



    #[Route('/api/mails_template', name: 'app_mails_template')]
    public function getTemplates(MailRepository $mailRepository): Response
    {
        return $this->json($mailRepository->findBy(['is_template' => true], ['name' => 'ASC']), Response::HTTP_OK, [], ['groups' => ['collection:mail']]);
    }


    #[Route('/api/send-mail/{mailKey}', name: 'app_send_mail', methods: ['POST'])]
    public function sendMail(EncryptionService $encryptionService, string $mailKey, MailRepository $mailTemplateRepository, Request $request, ApiKeyRepository $apiKeyRepository): Response
    {

        $apiKey = $request->query->get('apiKey');
        $user = $apiKeyRepository->findOneBy(['token' => $apiKey])->getUser();

        if (!$user) {
            return $this->json(['message' => "Veuillez passer une clé d'API valide"], Response::HTTP_UNAUTHORIZED);
        }

        $mail = $mailTemplateRepository->findOneBy(['user' => $user, 'token' => $mailKey]);
        if (!$mail) {
            return $this->json(['message' => 'Une erreur est survenue, le mail est introuvable'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$mail->isDeploy()) {
            return $this->json(['message' => 'Veuillez activer votre mail dans votre dashboard'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $content = $request->getContent();
        $jsonData = json_decode($content, true);


        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format'], Response::HTTP_BAD_REQUEST);
        }

        $data = $jsonData['data'] ?? null;
        $receiver = $jsonData['receiver'] ?? null;

        if (!$data || !$receiver) {
            return $this->json(['message' => 'Les champs "data" et "receiver" sont obligatoires.'], Response::HTTP_BAD_REQUEST);
        }


        $smtp = $encryptionService->decrypt($user->getSmtp());
        dd($smtp);
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

    private function buildContent(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->convertArrayToString($value);
            }

            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return "
    <html>
    <head>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
        </style>
    </head>
    <body style='color: black; font-family: Inter, sans-serif;'>
        " . $content . "
    </body>
    </html>";
    }

// Méthode pour convertir un tableau en chaîne de caractères lisible
    private function convertArrayToString(array $array): string
    {
        $result = '';

        // Vérifier si le tableau est un tableau de tableaux (comme dans "products")
        foreach ($array as $item) {
            if (is_array($item)) {
                $result .= implode(', ', $item) . "\n"; // Convertir chaque tableau en chaîne
            } else {
                $result .= $item . "\n"; // Sinon, ajouter directement
            }
        }

        return $result;
    }
}
