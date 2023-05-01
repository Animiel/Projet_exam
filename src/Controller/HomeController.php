<?php

namespace App\Controller;

use App\Entity\Annonce;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $annonces = $doctrine->getRepository(Annonce::class)->findAll();
        return $this->render('home/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }
}
