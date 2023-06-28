<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sujet;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mailer\MailerInterface;       //ajouter dans les paramètres de fonction aussi
use Symfony\Component\Mime\Email;       // one peut utiliser un templatedemail aussi, si c'est le cas, suppression de cette ligne

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

    #[Route('/members', name: 'list_members')]
    public function members(ManagerRegistry $doctrine)
    {
        $users = $doctrine->getRepository(User::class)->findBy([], ['pseudo' => 'ASC']);

        return $this->render('home/members.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/annonce', name: 'create_annonce')]
    public function createAnnonce(ManagerRegistry $doctrine, Annonce $annonce = null, Request $request)
    {
        if (!$annonce) {
            $annonce = new Annonce();
        }

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $annonce = $form->getData();
            $annonce->setAnnonceUser($user);

            $imgFile = $form->get('image')->getData(); // Récupère le fichier uploadé via le champ dédié
            if ($imgFile) {
                $fileName = uniqid().'.'.$imgFile->guessExtension(); // Crée un nom unique pour le fichier
                $imgFile->move('img/annonces', $fileName); // Déplace le fichier vers le dossier de destination
                $annonce->setImage($fileName);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('home/annonce.html.twig', [
            'formAnnonce' => $form->createView(),
        ]);
    }

    #[Route('/notif/{id}', name: 'notif_annonce')]
    // #[ParamConverter("Annonce", options:["mapping" => ["aid" => "annonceUser"]])]
    // #[ParamConverter("User", options:["mapping" => ["uid" => "id"]])]
    public function notification(User $annonceur, MailerInterface $mailer)
    {
        $user = $this->getUser();
        $userEmail = $user->getEmail();
        $annonceurEmail = $annonceur->getEmail();
        $email = (new Email())
                ->from('noreply@petseek.com')
                ->to($annonceurEmail)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Quelqu\'un a peut-être une info sur votre animal disparu !')
                ->text($userEmail.' a une info sur votre animal. Pour votre sécurité : Méfiez vous des pièges et prenez les mesures nécessaires avant de rencontrer ou contacter cette personne !');
                // ->html('<p>See Twig integration for better HTML integration!</p>');
    
        $mailer->send($email);
        // dd($mailer);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/addAnnonceFav/{id}', name: 'add_afav')]
    public function addaFav(ManagerRegistry $doctrine, Annonce $annonce)
    {
        $user = $this->getUser();
        
        if(!$user->getAnnonceFavorites()->exists(function($test) use ($annonce) { return; })) {
            $entityManager = $doctrine->getManager();
            $user->addAnnonceFavorite($annonce);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        else {
            $flash = "Cette annonce est déjà dans vos favoris.";
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/removeAnnonceFav/{id}', name: 'remove_afav')]
    public function removeaFav(ManagerRegistry $doctrine, Annonce $annonce)
    {
        $user = $this->getUser();
        
        // if($user->getAnnonceFavorites()->exists(function($test) use ($annonce) { return; })) {
            $entityManager = $doctrine->getManager();
            $user->removeAnnonceFavorite($annonce);
            $entityManager->flush();
        // }
        // else {
            // $flash = "Cette annonce n'est pas dans vos favoris.";
        // }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/annoncesFav', name: 'annonces_fav')]
    public function aFav(ManagerRegistry $doctrine)
    {
        $annonces = $doctrine->getRepository(Annonce::class)->findFav();

        return $this->render('home/aFav.html.twig', [
            'annonces' => $annonces,
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
        $sujets = $doctrine->getRepository(Sujet::class)->findFav();

        return $this->render('home/sujetsFav.html.twig', [
            'sujets' => $sujets,
        ]);
    }
}
