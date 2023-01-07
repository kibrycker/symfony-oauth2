<?php

namespace App\EventListener;

use Exception;
use League\Bundle\OAuth2ServerBundle\Event\UserResolveEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Предоставление токена доступа пользователю
 */
final class UserResolveListener
{
    /**
     * Определение зависимостей
     *
     * @param UserProviderInterface $userProvider
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly UserProviderInterface       $userProvider,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly LoggerInterface             $logger
    ) {}

    /**
     * Привязывание пользователя к токену клиента
     *
     * @throws Exception
     */
    public function onUserResolve(UserResolveEvent $event): void
    {
        try {
            $user = $this->userProvider->loadUserByIdentifier($event->getUsername());
        } catch (AuthenticationException $e) {
            $this->logger->error($e->getMessage(), [$e]);
            throw $e;
        }

        if (!($user instanceof PasswordAuthenticatedUserInterface)) {
            $e = new Exception('$user not instanceof PasswordAuthenticatedUserInterface', 500);
            $this->logger->error($e->getMessage(), [$e]);
            throw $e;
        }

        if (!$this->userPasswordHasher->isPasswordValid($user, $event->getPassword())) {
            $e = new Exception('Password not valid');
            $this->logger->info($e->getMessage(), [$e]);
            throw $e;
        }

        $event->setUser($user);
    }
}