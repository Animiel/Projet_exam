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
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    #[ParamConverter("sujet", options: ["mapping" => ["idSuj" => "id"]])]
    #[ParamConverter("message", options: ["mapping" => ["idMsg" => "id"]])]
    public function afficherMessages(ManagerRegistry $doctrine, Sujet $sujet, Request $request, Message $message = null)
    {
        //on crée le formulaire de création de message
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        //on récupère les infos de l'utilisateur actuellement connecté
        $user = $this->getUser();
        //on crée une variable booléenne pour gérer la modification de message
        $edit = true;

        //si le message n'existe pas, on en crée un et on actualise la variable d'édition à false
        if (!$message) {
            $message = new Message();
            $edit = false;
        } else {
            //sinon on réinitialise l'array d'images
            $message->setImages([]);
        }

        //si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $uploadedFiles = $form->get('images')->getData();

            //on donne un nom unique à chaque image et on place les images dans le dossier public/img/posts
            foreach ($uploadedFiles as $image) {
                $fileName = uniqid() . '.' . $image->guessExtension();
                array_push($uploadedFiles, $fileName);
                $image->move('img/posts', $fileName);
                unset($image);
            }
            //on met à jour le champ images du message avec la nouvelle liste d'images
            $message->setImages($uploadedFiles);

            //on met à jour les champs sans l'interaction de l'utilisateur
            $message->setUpdatedDate(new \DateTime());
            $message->setMsgUser($user);
            //si c'est un nouveau message
            if (!$edit) {
                //on stocke la date de publication et sa première version dans des champs prévus à cet effet
                $message->setPublicationDate(new \DateTime());
                $message->setOriginal($form->get('contenu')->getData());
            }
            //on inclus le sujet auquel le message appartient
            $message->setSujet($sujet);

            //on met à jour la base de données
            $entityManager = $doctrine->getManager();
            //on prépare les changements "en file d'attente"
            $entityManager->persist($message);
            //on les change définitivement dans la base de données
            $entityManager->flush();

            //message de succès
            $this->addFlash(
                'success',
                'Message posté.'
            );

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
        //on crée le formulaire suivant son modèle
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        //si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            //on récupère le fichier
            $imgFile = $form->get('image')->getData();
            if ($imgFile) {
                //on lui donne un nom unique
                $fileName = uniqid() . '.' . $imgFile->guessExtension();
                //on le met dans le dossier voulu
                $imgFile->move('img/categories', $fileName);
                //et on attribut le nouveau nom du fichier à son champ en base de donnée
                $categorie->setImage($fileName);
            }
            //la date de création correspond à la date actuelle lors de l'envoi
            $categorie->setCreationDate(new \DateTime());

            //on met à jour la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            //message de succès
            $this->addFlash(
                'success',
                'Nouvelle catégorie créée.'
            );

            return $this->redirectToRoute('app_forum');
        }

        return $this->render('forum/formCtg.html.twig', [
            'formCtg' => $form->createView(),
        ]);
    }

    #[Route('/creerSuj/{idCtg}', name: 'crea_suj')]
    #[ParamConverter("ctg", options: ["mapping" => ["idCtg" => "id"]])]
    public function newSuj(ManagerRegistry $doctrine, Sujet $sujet = null, Request $request, Categorie $ctg)
    {
        //on crée le formulaire suivant son modèle
        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);
        //on récupère l'utilisateur connecté
        $user = $this->getUser();

        //si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $sujet = $form->getData();

            //on met à jour les champs de l'entité Sujet
            $sujet->setCreationDate(new \DateTime());
            $sujet->setSujUser($user);
            $sujet->setCategorie($ctg);
            //par défaut le sujet est ouvert
            $sujet->setClosed(0);

            //on met à jour la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($sujet);
            $entityManager->flush();

            //message de succès
            $this->addFlash(
                'success',
                'Nouveau sujet créé.'
            );

            return $this->redirectToRoute('sujets_categorie', ['id' => $ctg->getId()]);
        }

        return $this->render('forum/formSuj.html.twig', [
            'formSuj' => $form->createView(),
        ]);
    }

    #[Route('/addSujFav/{id}', name: 'add_sujfav')]
    public function addSujFav(ManagerRegistry $doctrine, Sujet $sujet)
    {
        //on récupère l'utilisateur connecté
        $user = $this->getUser();

        //on vérifie si le sujet n'est pas dans la collection
        if (!$user->getSujetFavorites()->exists(function ($test) use ($sujet) {
            return;
        })) {
            //et on l'ajoute s'il n'existe pas
            $entityManager = $doctrine->getManager();
            $user->addSujetFavorite($sujet);
            $entityManager->persist($user);
            $entityManager->flush();

            //message de succès s'il est ajouté
            $this->addFlash(
                'success',
                'Sujet ajouté à vos favoris.'
            );
        } else {
            //sinon on prévient l'utilisateur que le sujet est déjà enregistré
            $this->addFlash(
                'warning',
                'Ce sujet est déjà dans vos favoris.'
            );
        }

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/removeSujFav/{id}', name: 'remove_sujfav')]
    public function removeSujFav(ManagerRegistry $doctrine, Sujet $sujet)
    {
        //on récupère l'utilissateur connecté
        $user = $this->getUser();

        //on retire le sujet des favoris de l'utilisateur
        $entityManager = $doctrine->getManager();
        $user->removeSujetFavorite($sujet);
        $entityManager->flush();

        //message de succès
        $this->addFlash(
            'success',
            'Sujet retiré de vos favoris.'
        );

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/sujetsFav', name: 'sujets_fav')]
    public function sujetsFav(ManagerRegistry $doctrine)
    {
        return $this->render('home/favoris.html.twig', []);
    }

    #[Route('/closeSuj/{idSuj}/{idCtg}', name: 'close_suj')]
    #[ParamConverter("sujet", options: ["mapping" => ["idSuj" => "id"]])]
    #[ParamConverter("ctg", options: ["mapping" => ["idCtg" => "id"]])]
    public function close(ManagerRegistry $doctrine, Sujet $sujet, Categorie $ctg)
    {
        //on ferme le sujet
        $sujet->setClosed(1);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($sujet);
        $entityManager->flush();

        return $this->redirectToRoute('sujets_categorie', ['id' => $ctg->getId()]);
    }

    #[Route('/openSuj/{idSuj}/{idCtg}', name: 'open_suj')]
    #[ParamConverter("sujet", options: ["mapping" => ["idSuj" => "id"]])]
    #[ParamConverter("ctg", options: ["mapping" => ["idCtg" => "id"]])]
    public function open(ManagerRegistry $doctrine, Sujet $sujet, Categorie $ctg)
    {
        //on ouvre le sujet
        $sujet->setClosed(0);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($sujet);
        $entityManager->flush();

        return $this->redirectToRoute('sujets_categorie', ['id' => $ctg->getId()]);
    }
}
