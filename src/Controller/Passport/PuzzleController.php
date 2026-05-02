<?php

namespace App\Controller\Passport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/passport')]
class PuzzleController extends AbstractController
{
    #[Route('', name: 'passport_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('home');
    }
}