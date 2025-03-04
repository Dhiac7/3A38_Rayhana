<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class TranslationController extends AbstractController
{
    #[Route('/test-translation', name: 'test_translation')]
    public function index(TranslatorInterface $translator): Response
    {
        $message = $translator->trans('hello');

        return new Response("<h1>$message</h1>");
    }
}

