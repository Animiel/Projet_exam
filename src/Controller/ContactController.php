<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(ManagerRegistry $doctrine, Contact $contact = null, Request $request, MailerInterface $mailer): Response
    {
        //on crée le formulaire de contact grâce à son modèle ContactType
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        //si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            //on envoie l'email au(x) responsable(s) du site en récupérant les informations des champs du formulaire
            $email = (new Email())
                ->from($form->get('email')->getData())
                ->to('serviceadmin@petseek.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($form->get('sujet')->getData())
                ->text($form->get('message')->getData() . '<br>' . $form->get('nom')->getData() . ' ' . $form->get('prenom')->getData());
            // ->html('<p>See Twig integration for better HTML integration!</p>');

            //on envoie le mail
            $mailer->send($email);

            //on stocke le message dans la base de données pour y accéder si besoin
            $entityManager = $doctrine->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            //message de succès en cas d'envoi
            $this->addFlash(
                'success',
                'Message envoyé, quelqu\'un vous recontactera dans les plus brefs délais.'
            );

            //on revient sur la page de contact
            return $this->redirectToRoute('app_contact');
        }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
