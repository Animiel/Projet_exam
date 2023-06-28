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
        //on récupère toutes les catégories en les classant par ordre alphabétique croissant (A -> Z)
        $categories = $doctrine->getRepository(Categorie::class)->findBy(
            [],
            ['name' => 'ASC']
        );

        //on envoie la liste sur la vue correspondante
        return $this->render('forum/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/forum/{id}', name: 'sujets_categorie')]
    public function afficherSujets(Categorie $categorie)
    {
        return $this->render('forum/sujets.html.twig', [
            'categorie' => $categorie
        ]);
    }

    #[Route('/forum/sujet/{idSuj}', name: 'messages_sujet')]
    #[Route('/forum/sujet/{idSuj}/edit/{idMsg}', name: 'edit_msg')]
    #[ParamConverter("sujet", options:["mapping" => ["idSuj" => "id"]])]
    #[ParamConverter("message", options:["mapping" => ["idMsg" => "id"]])]
    public function afficherMessages(ManagerRegistry $doctrine, Sujet $sujet, Request $request, Message $message = null)
    {
        //si le message n'existe pas, on en crée un
        if(!$message) {
            $message = new Message();
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        //on récupère les infos de l'utilisateur actuellement connecté
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();

            // $imgFile = $form->get('image')->getData();
            // if ($imgFile) {
            //     $fileName = uniqid().'.'.$imgFile->guessExtension();
            //     $imgFile->move('img/categories', $fileName);
            //     $categorie->setImage($fileName);
            // }

            //on met à jour les champs sans l'interaction de l'utilisateur
            $message->setPublicationDate(new \DateTime());
            $message->setMsgUser($user);
            $message->setSujet($sujet);
            if($message) {
                $message->setUpdatedDate(new \DateTime());
            }

            //on met à jour la base de données
            $entityManager = $doctrine->getManager();
            //on prépare les changements "en file d'attente"
            $entityManager->persist($message);
            //on les change définitivement dans la base de données
            $entityManager->flush();

            return $this->redirectToRoute('messages_sujet', ['idSuj' => $sujet->getId()]);
        }
        
        return $this->render('forum/messages.html.twig', [
            //on transmet les éléments à la vue
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

            //on récupère le fichier
            $imgFile = $form->get('image')->getData();
            if ($imgFile) {
                //on lui donne un nom unique
                $fileName = uniqid().'.'.$imgFile->guessExtension();
                //on le met dans le dossier voulu
                $imgFile->move('img/categories', $fileName);
                //et on attribut le nouveau nom du fichier à son champ en base de donnée
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
        
        //on vérifie si l'annonce n'est pas dans la collection
        if(!$user->getSujetFavorites()->exists(function($test) use ($sujet) { return; })) {
            //et on l'ajoute si c'est le cas
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

    #[Route('/closeSuj/{idSuj}/{idCtg}', name: 'close_suj')]
    #[ParamConverter("sujet", options:["mapping" => ["idSuj" => "id"]])]
    #[ParamConverter("ctg", options:["mapping" => ["idCtg" => "id"]])]
    public function close(ManagerRegistry $doctrine, Sujet $sujet, Categorie $ctg)
    {
        $sujet->setClosed(1);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($sujet);
        $entityManager->flush();

        return $this->redirectToRoute('sujets_categorie', ['id' => $ctg->getId()]);
    }

    #[Route('/openSuj/{idSuj}/{idCtg}', name: 'open_suj')]
    #[ParamConverter("sujet", options:["mapping" => ["idSuj" => "id"]])]
    #[ParamConverter("ctg", options:["mapping" => ["idCtg" => "id"]])]
    public function open(ManagerRegistry $doctrine, Sujet $sujet, Categorie $ctg)
    {
        $sujet->setClosed(0);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($sujet);
        $entityManager->flush();

        return $this->redirectToRoute('sujets_categorie', ['id' => $ctg->getId()]);
    }
}
