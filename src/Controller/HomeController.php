<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Annonce;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
