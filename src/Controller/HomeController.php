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

        //on définit le formulaire de filtrage
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        //si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //si tous les champs de filtrage sont vide ou nul
            if (($searchData->q == '' || $searchData->q == null) && ($searchData->local == '' || $searchData->local == null) && ($searchData->motif == '' || $searchData->motif == null) && ($searchData->genre == 'None')) {
                //on renvoit à la page d'accueil
                return $this->redirectToRoute('app_home');
            } else {
                //sinon on affiche la liste des annonces trouvées
                $annoncesSearch = $aRepo->findBySearch($searchData, $page);
            }

            //on ajoute compte le nombre d'annonces trouvées et on le notifie à l'utilisateur
            $this->addFlash(
                'notice',
                '' . count($annoncesSearch) . ' résultats trouvés.'
            );

            return $this->render('home/index.html.twig', [
                'searchForm' => $form->createView(),
                'annonces' => $annoncesSearch,
            ]);
        }

        //on cherche les annonces par page (avec une limite de 2 annonces par page)
        $annonces = $doctrine->getRepository(Annonce::class)->annoncesPaginated($page, 2);
        //on instancie un nouvel objet Finder
        $finder = new Finder();
        $images = [];
        //on recherche tous les fichiers dans le dossier public/img/annonces dont l'extension correspond à jpg, jpeg, ou png
        $finder->files()->in('img/annonces')->name(['*.jpg', '*.png', '*.jpeg']);
        //pour chaque fichier trouvé on récupère le nom des fichiers et on les stocke dans une array
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
        //on récupère la liste des membres inscrits en les affichant par ordre alphabétique de pseudos
        $users = $doctrine->getRepository(User::class)->findBy([], ['pseudo' => 'ASC']);

        return $this->render('home/members.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/annonce', name: 'create_annonce')]
    public function createAnnonce(ManagerRegistry $doctrine, Annonce $annonce = null, Request $request)
    {
        //si l'annonce n'existe pas encore on en crée une nouvelle instance
        if (!$annonce) {
            $annonce = new Annonce();
        }

        //on crée le formulaire à partir de AnnonceType
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        //si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //on récupère l'utilisateur actuel ayant envoyé le formulaire
            $user = $this->getUser();
            //les données des champs sont récupérées dans la variable annonce
            $annonce = $form->getData();
            //on récupère indépendemment les images du formulaire
            $uploadedFiles = $form->get('images')->getData();

            //pour chaque image
            foreach ($uploadedFiles as $image) {
                //on leur donne un nom unique en conservant leur extension
                $fileName = uniqid() . '.' . $image->guessExtension();
                //on remet l'image avec son nouveau nom dans l'array
                array_push($uploadedFiles, $fileName);
                //et on déplace l'image dans le dossier public/img/annonces
                $image->move('img/annonces', $fileName);
                unset($image);
            }

            //on définit la valeur de la date de publication avec la date lors de l'envoi du formulaire
            $annonce->setPublicationDate(new \Datetime());
            //on complète les infos avec les valeurs gérées précédemment (images et utilisateur)
            $annonce->setImages($uploadedFiles);
            $annonce->setAnnonceUser($user);

            //on envoit tout à la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            //message de confirmation
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
        //on récupère l'utilisateur effectuant l'action
        $user = $this->getUser();
        //on récupère les informations de l'utilisateur ayant posté une annonce
        $userEmail = $user->getEmail();
        $annonceurEmail = $annonceur->getEmail();
        //on crée une instance de mail
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

        //on envoie le mail à l'annonceur
        $mailer->send($email);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/addAnnonceFav/{id}', name: 'add_afav')]
    public function addaFav(ManagerRegistry $doctrine, Annonce $annonce)
    {
        //on récupère l'utilisateur qui fait l'action
        $user = $this->getUser();

        //si l'annonce n'existe pas dans la liste des annonces favorites de l'utilisateur
        if (!$user->getAnnonceFavorites()->exists(function ($test) use ($annonce) {
            return;
        })) {
            //on ajoute l'annonce dans les favoris
            $entityManager = $doctrine->getManager();
            $user->addAnnonceFavorite($annonce);
            $entityManager->persist($user);
            $entityManager->flush();

            //et on affiche un message de succès
            $this->addFlash(
                'success',
                'Annonce ajoutée aux favoris.'
            );
        } else {
            //sinon on affiche un message d'échec pour prévenir que l'annonce est déjà en favoris
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

        //on retire l'annonce des favoris
        $entityManager = $doctrine->getManager();
        $user->removeAnnonceFavorite($annonce);
        $entityManager->flush();

        //message de succès
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
