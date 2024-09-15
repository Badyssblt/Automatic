<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Mail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;


#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class MailSetOwnerProcessor implements ProcessorInterface
{
    private Security $security;

    public function __construct(Security $security,
                                #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
                                private ProcessorInterface $persistProcessor)
    {
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Mail && $data->getUser() === null) {
            $currentUser = $this->security->getUser();
            if ($currentUser) {
                $data->setUser($currentUser);
            }
            if ($data->isTemplate() === true && !in_array("ROLE_ADMIN", $currentUser->getRoles(), true)) {
                $data->setTemplate(false);
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
