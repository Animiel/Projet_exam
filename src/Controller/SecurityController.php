<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sujet;
use App\Entity\Annonce;
use App\Entity\Message;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/bannir/{id}', name: 'ban_user')]
    public function bannir(User $user, ManagerRegistry $doctrine)
    {
        $user->setBanni(1);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/supprMsg/{idSuj}/{idMsg}', name: 'suppr_msg')]
    #[ParamConverter("msg", options:["mapping" => ["idMsg" => "id"]])]
    #[ParamConverter("sujet", options:["mapping" => ["idSuj" => "id"]])]
    public function supprMsg(ManagerRegistry $doctrine, Message $msg, Sujet $sujet)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($msg);
        $entityManager->flush();

        return $this->redirectToRoute('messages_sujet', ['idSuj' => $sujet->getId()]);
    }

    #[Route('/supprAnnonce/{id}', name: 'suppr_annonce')]
    public function supprAnnonce(ManagerRegistry $doctrine, Annonce $annonce)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($annonce);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/supprSujet/{idCtg}/{idSuj}', name: 'suppr_sujet')]
    #[ParamConverter("ctg", options:["mapping" => ["idCtg" => "id"]])]
    #[ParamConverter("sujet", options:["mapping" => ["idSuj" => "id"]])]
    public function supprSujet(ManagerRegistry $doctrine, Sujet $sujet, Categorie $ctg)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($sujet);
        $entityManager->flush();
        
        return $this->redirectToRoute('sujets_categorie', ['id' => $ctg->getId()]);
    }

    #[Route('/supprCtg/{id}', name: 'suppr_ctg')]
    public function supprCtg(ManagerRegistry $doctrine, Categorie $categorie)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/unban/{id}', name: 'unban_user')]
    public function unban(ManagerRegistry $doctrine, User $user)
    {
        $user->setBanni(0);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
