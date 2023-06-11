<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    #[Route('/notif/{aid}/{uid}', name: 'notif_annonce')]
    #[ParamConverter("Annonce", options:["mapping" => ["aid" => "annonceUser"]])]
    #[ParamConverter("User", options:["mapping" => ["uid" => "id"]])]
    public function notification(User $user, User $annonceur, MailerInterface $mailer)
    {
        $email = (new Email())
                ->from('noreply@petseek.com')
                ->to($annonceur->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Quelqu\'un a peut-être une info sur votre animal disparu !')
                ->text($user->getEmail().' a une info sur votre animal. Pour votre sécurité : Méfiez vous des pièges et prenez les mesures nécessaires avant de rencontrer cette personne !');
                // ->html('<p>See Twig integration for better HTML integration!</p>');
    
        $mailer->send($email);

        return $this->redirectToRoute('app_home');
    }
}
