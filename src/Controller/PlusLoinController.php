<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlusLoinController extends AbstractController
{
    #[Route('/plus/loin', name: 'app_plus_loin')]
    public function index(): Response
    {
        return $this->render('plus_loin/index.html.twig', [
            'controller_name' => 'PlusLoinController',
        ]);
    }
}
