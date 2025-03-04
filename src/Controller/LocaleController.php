<?php
// src/Controller/LocaleController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class LocaleController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale($locale, Request $request): Response
    {
        $request->getSession()->set('_locale', $locale);

        return $this->redirectToRoute('app_translation');
    }

}
