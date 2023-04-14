<?php

namespace App\Controller;

use App\Entity\Sujet;
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

    #[Route('/forum/sujet/{id}', name: 'messages_sujet')]
    public function afficherMessages(ManagerRegistry $doctrine, Sujet $sujet) {
        return $this->render('forum/messages.html.twig', [
            'sujet' => $sujet
        ]);
    }
}
