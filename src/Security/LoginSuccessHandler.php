<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        // Rediriger selon le rôle
        if (in_array('ROLE_MEDECIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
        }

        if (in_array('ROLE_PATIENT', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_patient_dashboard'));
        }

        // Par défaut, rediriger vers la page d'accueil
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}

