<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Entity\Message;
use App\Form\SujetType;
use App\Entity\Categorie;
use App\Form\MessageType;
use App\Form\CategorieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
    public function afficherMessages(ManagerRegistry $doctrine, Sujet $sujet, Request $request, Message $message = null)
    {
        if(!$message) {
            $message = new Message();
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();

            // $imgFile = $form->get('image')->getData();
            // if ($imgFile) {
            //     $fileName = uniqid().'.'.$imgFile->guessExtension();
            //     $imgFile->move('img/categories', $fileName);
            //     $categorie->setImage($fileName);
            // }
            $message->setPublicationDate(new \DateTime());
            $message->setMsgUser($user);
            $message->setSujet($sujet);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('messages_sujet', ['id' => $sujet->getId()]);
        }
        
        return $this->render('forum/messages.html.twig', [
            'sujet' => $sujet,
            'formMsg' => $form->createView(),
        ]);
    }

    #[Route('/creerCtg', name: 'crea_ctg')]
    public function newCtg(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request)
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            $imgFile = $form->get('image')->getData();
            if ($imgFile) {
                $fileName = uniqid().'.'.$imgFile->guessExtension();
                $imgFile->move('img/categories', $fileName);
                $categorie->setImage($fileName);
            }
            $categorie->setCreationDate(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum');
        }
        
        return $this->render('forum/formCtg.html.twig', [
            'formCtg' => $form->createView(),
        ]);
    }

    #[Route('/creerSuj/{id}', name: 'crea_suj')]
    #[ParamConverter("categorie", options:["mapping" => ["id" => "id"]])]
    public function newSuj(ManagerRegistry $doctrine, Sujet $sujet = null, Request $request, Categorie $ctg)
    {
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);
        $user = $this->getUser();
        

        if ($form->isSubmitted() && $form->isValid()) {
            $sujet = $form->getData();

            $sujet->setCreationDate(new \DateTime());
            $sujet->setSujUser($user);
            $sujet->setCategorie($ctg);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($sujet);
            $entityManager->flush();

            return $this->redirectToRoute('sujets_categorie', ['id' => $ctg->getId()]);
        }
        
        return $this->render('forum/formSuj.html.twig', [
            'formSuj' => $form->createView(),
        ]);
    }

    #[Route('/addSujFav/{id}', name: 'add_sujfav')]
    public function addSujFav(ManagerRegistry $doctrine, Sujet $sujet)
    {
        $user = $this->getUser();
        
        if(!$user->getSujetFavorites()->exists(function($test) use ($sujet) { return; })) {
            $entityManager = $doctrine->getManager();
            $user->addSujetFavorite($sujet);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        else {
            $flash = "Ce sujet est déjà dans vos favoris.";
        }

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/removeSujFav/{id}', name: 'remove_sujfav')]
    public function removeSujFav(ManagerRegistry $doctrine, Sujet $sujet)
    {
        $user = $this->getUser();
        
        $entityManager = $doctrine->getManager();
        $user->removeSujetFavorite($sujet);
        $entityManager->flush();

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/sujetsFav', name: 'sujets_fav')]
    public function sujetsFav(ManagerRegistry $doctrine)
    {
        return $this->render('home/sujetsFav.html.twig', []);
    }
}
