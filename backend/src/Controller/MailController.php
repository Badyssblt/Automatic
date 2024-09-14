<?php

namespace App\Controller;

use App\Repository\ApiKeyRepository;
use App\Repository\MailTemplateRepository;
use App\Repository\UserRepository;
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
    public function index(MailTemplateRepository $mailTemplateRepository): Response
    {
        $mails = $mailTemplateRepository->findBy(['user' => $this->getUser()], ['name' => 'ASC']);

        return $this->json($mails, Response::HTTP_OK, [], ['groups' => ['collection:mail']]);
    }


    #[Route('/api/send-mail/{mailKey}', name: 'app_send_mail', methods: ['POST'])]
    public function sendMail(string $mailKey, MailTemplateRepository $mailTemplateRepository, Request $request, ApiKeyRepository $apiKeyRepository): Response
    {

        // Récupération de l'API Key
        $apiKey = $request->query->get('apiKey');
        $user = $apiKeyRepository->findOneBy(['token' => $apiKey])->getUser();

        // Vérification de l'utilisateur
        if (!$user) {
            return $this->json(['message' => "Veuillez passer une clé d'API valide"], Response::HTTP_UNAUTHORIZED);
        }

        // Récupération du modèle de mail
        $mail = $mailTemplateRepository->findOneBy(['user' => $user, 'token' => $mailKey]);
        if (!$mail) {
            return $this->json(['message' => 'Une erreur est survenue, le mail est introuvable'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$mail->isDeploy()) {
            return $this->json(['message' => 'Veuillez activer votre mail dans votre dashboard'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Récupération du contenu de la requête JSON
        $content = $request->getContent();
        $jsonData = json_decode($content, true);


        // Vérifier si le JSON a été bien décodé
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer les données et le destinataire depuis le JSON
        $data = $jsonData['data'] ?? null;
        $receiver = $jsonData['receiver'] ?? null;

        // Vérifier la présence des données
        if (!$data || !$receiver) {
            return $this->json(['message' => 'Les champs "data" et "receiver" sont obligatoires.'], Response::HTTP_BAD_REQUEST);
        }


        // Configuration SMTP et envoi du mail
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

    private function buildContent(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            // Vérifier si la valeur est un tableau (comme 'products')
            if (is_array($value)) {
                // Si c'est un tableau, créer une représentation lisible
                $value = $this->convertArrayToString($value);
            }

            // Remplacer les variables dans le contenu avec les valeurs correspondantes
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
