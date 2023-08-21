<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sujet;
use App\Entity\Annonce;
use App\Form\SearchType;
use App\Form\AnnonceType;
use App\Model\SearchData;
use Symfony\Component\Finder\Finder;
use App\Repository\AnnonceRepository;
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
    public function index(ManagerRegistry $doctrine, Request $request, AnnonceRepository $aRepo): Response
    {
        //on cherche le numéro de page dans l'url, par défaut on se trouvera sur la page 1
        $page = $request->query->getInt('page', 1);

        $searchData = new SearchData();

        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (($searchData->q == '' || $searchData->q == null) && ($searchData->local == '' || $searchData->local == null) && ($searchData->motif == '' || $searchData->motif == null) && ($searchData->genre == 'None')) {
                return $this->redirectToRoute('app_home');
            } else {
                $annoncesSearch = $aRepo->findBySearch($searchData, $page);
            }

            $this->addFlash(
                'notice',
                '' . count($annoncesSearch) . ' résultats trouvés.'
            );

            return $this->render('home/index.html.twig', [
                'searchForm' => $form->createView(),
                'annonces' => $annoncesSearch,
            ]);
        }

        //on cherche les annonces par page
        $annonces = $doctrine->getRepository(Annonce::class)->annoncesPaginated($page, 2);
        $finder = new Finder();
        $images = [];
        $finder->files()->in('img/annonces')->name(['*.jpg', '*.png', '*.jpeg']);
        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();
            $images[] = $fileNameWithExtension;
        }
        return $this->render('home/index.html.twig', [
            'annonces' => $annonces,
            'images' => $images,
            'searchForm' => $form->createView(),
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
            $uploadedFiles = $form->get('images')->getData();

            foreach ($uploadedFiles as $image) {
                $fileName = uniqid() . '.' . $image->guessExtension();
                array_push($uploadedFiles, $fileName);
                $image->move('img/annonces', $fileName);
                unset($image);
            }

            $annonce->setPublicationDate(new \Datetime());
            $annonce->setImages($uploadedFiles);
            $annonce->setAnnonceUser($user);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Nouvelle annonce créée avec succès.'
            );

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
            ->text($userEmail . ' a une info sur votre animal. Pour votre sécurité : Méfiez vous des pièges et prenez les mesures nécessaires avant de rencontrer ou contacter cette personne !');
        // ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        // dd($mailer);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/addAnnonceFav/{id}', name: 'add_afav')]
    public function addaFav(ManagerRegistry $doctrine, Annonce $annonce)
    {
        $user = $this->getUser();

        if (!$user->getAnnonceFavorites()->exists(function ($test) use ($annonce) {
            return;
        })) {
            $entityManager = $doctrine->getManager();
            $user->addAnnonceFavorite($annonce);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Annonce ajoutée aux favoris.'
            );
        } else {
            $this->addFlash(
                'warning',
                'Cette annonce est déjà dans vos favoris.'
            );
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/removeAnnonceFav/{id}', name: 'remove_afav')]
    public function removeaFav(ManagerRegistry $doctrine, Annonce $annonce)
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $user->removeAnnonceFavorite($annonce);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Annonce supprimée de vos favoris.'
        );

        return $this->redirectToRoute('app_home');
    }

    #[Route('/annoncesFav', name: 'annonces_fav')]
    public function aFav(ManagerRegistry $doctrine)
    {
        return $this->render('home/aFav.html.twig', []);
    }

    #[Route('/myAnnonces', name: 'my_annonces')]
    public function myAnnonces()
    {
        return $this->render('home/myAnnonces.html.twig', []);
    }
}
