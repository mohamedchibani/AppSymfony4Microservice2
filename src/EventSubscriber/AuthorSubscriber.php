<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class AuthorSubscriber implements EventSubscriberInterface
{
    public $myToken;
    public function __construct(TokenStorageInterface $myToken)
    {
        $this->myToken = $myToken;
    }

    public function getUserFromToken(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        
        if(($entity instanceof Post || $entity instanceof Comment) && $method == Request::METHOD_POST){
            
            $author = $this->myToken->getToken()->getUser();
            $entity->setAuthor($author);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.view' => ['getUserFromToken' , EventPriorities::PRE_WRITE],
        ];
    }
}
