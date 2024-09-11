<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\MailTemplate;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class UserVerificationProcessor implements ProcessorInterface
{

    public function __construct(private HttpClientInterface $client,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
                                private ProcessorInterface $persistProcessor,
                                private JWTTokenManagerInterface $jwtManager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if($data instanceof User) {

            $verificationCode = random_int(100000, 999999);

            $data->setVerificationCode($verificationCode);

        }

        try {
            $this->client->request(
                'POST',
                $_ENV['API_URL'] ."/api/send-mail/549fd50a-2e8c-4e03-88ac-b5937adfb787?apiKey=test",
                [
                    "json" => [
                        "data" => $verificationCode,
                        "receiver" => $data->getEmail(),
                    ]
                ]
            );
        } catch (\Exception $e) {
            throw new \RuntimeException("Mail service unavailable: " . $e->getMessage());
        }


        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);

    }
}