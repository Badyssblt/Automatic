<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\MailTemplate;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


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
        if($data instanceof MailTemplate && $data->getUser() === null) {
            $currentUser = $this->security->getUser();
            if ($currentUser) {
                $data->setUser($currentUser);
            }
        }

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        return $result;
    }
}
