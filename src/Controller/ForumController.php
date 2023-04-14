<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{
    #[Route('/forum', name: 'app_forum')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(Categorie::class)->findBy(
            [],
            ['name' => 'ASC']
        );
        return $this->render('forum/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/forum/{id}', name: 'sujets_categorie')]
    public function afficherSujets(Categorie $categorie) {
        return $this->render('forum/sujets.html.twig', [
            'categorie' => $categorie
        ]);
    }
}
