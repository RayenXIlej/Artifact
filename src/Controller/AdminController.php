<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;




class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $repo): Response
    {
        $userVet = $repo->findByType("veterinaire");
        $userPetOwner = $repo->findByType("petOwner");
        $userPetSitter = $repo->findByType("petSitter");
        $user = $repo->findAll();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'userVets' => $userVet,
            'userPetOwners' => $userPetOwner,
            'user' => $user,
            'userPetSitters' => $userPetSitter
        ]);
    }

    #[Route('/gererVet', name: 'gererVet')]
    public function gererVet(UserRepository $repo){
        $user = $repo->findByType("veterinaire");
        return $this->render('admin/gererVet.html.twig',[
            'users' => $user
        ]);
    }

    #[Route('/gererPetOwner', name: 'gererPetOwner')]
    public function gererPetOwner(UserRepository $repo){
        $user = $repo->findByType("petOwner");
        return $this->render('admin/gererPetOwner.html.twig',[
            'users' => $user
        ]);
    }

    #[Route('/gererPetSitter', name: 'gererPetSitter')]
    public function gererPetSitter(UserRepository $repo){
        $user = $repo->findByType("petSitter");
        return $this->render('admin/gererPetSitter.html.twig',[
            'users' => $user
        ]);
    }

    #[Route('/gererUsers', name: 'gererUsers')]
    public function gererUsers(UserRepository $repo, Request $request, PaginatorInterface $paginator){
        $user = $repo->findAll();

        $pagination = $paginator->paginate(
            $repo->paginationQuery(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('admin/gererUsers.html.twig',[
            
            'pagination' => $pagination
        ]);
    }

     
    #[Route('/removeVet/{id}', name: 'removeVet')]
    public function removeUser(User $user): Response{
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('gererVet');
    }

    #[Route('/removeVetAcces/{id}', name: 'removeVetAcces')]
    public function removeUserAcces(User $user): Response{
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('demandeAcces');
    }

    #[Route('/removePetOwner/{id}', name: 'removePetOwner')]
    public function removePetOwner(User $user): Response{
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('gererPetOwner');
    }

    #[Route('/removePetSitter/{id}', name: 'removePetSitter')]
    public function removePetSitter(User $user): Response{
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('gererPetSitter');
    }

    #[Route('/demandeAcces', name: 'demandeAcces')]
    public function demandeAcces(UserRepository $repo){
        $demandes = $repo->findByacces(0);
        return $this->render('admin/demandeAcces.html.twig',[
            'demandes' => $demandes
        ]);
    }

    #[Route('/accepter/{id}', name: 'accepter')]
    public function  update(ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $user = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $user->setAcces(1);
        $em = $doctrine->getManager();
        $em->flush();

        // envoyer un mail de validation de compte par l administrateur lui disant que son compte a été créé
        $email = (new Email())    
        ->from('ArtifactPidev@gmail.com')
        ->to($user->getEmail())
        ->subject('Validation de votre compte')
        ->html('<p> Cher/chère '.$user->getNom().' '.$user->getPrenom().',</p></br>'
            .'<p> Nous sommes ravis de vous accueillir dans notre application ! Nous sommes convaincus que vous allez apprécier les fonctionnalités que nous avons développées pour vous.</p></br>'
            .'<p> Votre compte a été créé avec succès. </p></br>'
            .'<p> Votre email est : '.$user->getEmail().'</p>'
            .'<p> Votre role est : '.$user->getType().'</p>'
            .'<p> Si vous avez des questions ou des commentaires sur l application, n hésitez pas à nous contacter à l adresse e-mail suivante : ArtifactPidev@gmail.com </p></br>'
            .'<p> Encore une fois, bienvenue dans notre application et merci de nous avoir choisi ! </p></br>Cordialement,</br>'
            .'<p> L équipe de Artifact </p>'
        );  
        $transport=(new GmailSmtpTransport('ArtifactPidev@gmail.com','niskijoybvwhekem'));
        $mailer=new Mailer($transport);
        $mailer->send($email);
        
        return $this->redirectToRoute('demandeAcces');
        
    }

    #[Route('/debloquer/{id}', name: 'debloquer')]
    public function debloqueUser(ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $user = $doctrine
        ->getRepository(User::class)
        ->find($id);
    $userType = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $user->setBloque(0);
        $userType->getType();
        $em = $doctrine->getManager();
        $em->flush();
        // envoyer un mail de debloquage par l administrateur lui disant que son compte a été créé
        $email = (new Email())    
        ->from('ArtifactPidev@gmail.com')
        ->to($user->getEmail())
        ->subject('Déblocage de votre compte')
        ->html('<p> Cher/chère '.$user->getNom().' '.$user->getPrenom().',</p></br>'
        .'<p> Nous avons le plaisir de vous informer que votre compte utilisateur sur notre site a été débloqué. Vous pouvez maintenant vous connecter à nouveau à votre compte et accéder à toutes les fonctionnalités du site.</p></br>'
        .'<p> Nous espérons que vous continuerez à profiter de nos services en ligne et nous vous remercions de votre patience et de votre compréhension pendant la période de blocage. </p></br>'
        .'<p> Nous tenons à vous rappeler que la sécurité de notre site et de nos utilisateurs est notre priorité absolue, nous vous invitons donc à respecter les conditions d utilisation et à ne pas effectuer d activités suspectes.</p>'
        .'<p> Nous vous remercions de votre compréhension et restons à votre disposition si vous avez des questions ou des préoccupations.</p>'
        .'<p> Si vous avez des questions ou des commentaires sur l application, n hésitez pas à nous contacter à l adresse e-mail suivante : ArtifactPidev@gmail.com </p></br>'
        .'</br>Cordialement,</br>'
        .'<p> L équipe de Artifact </p>'
        );  
        $transport=(new GmailSmtpTransport('ArtifactPidev@gmail.com','niskijoybvwhekem'));
        $mailer=new Mailer($transport);
        $mailer->send($email);

        if ($user->getType() == "veterinaire") {
            return $this->redirectToRoute("gererVet");
        } elseif ($user->getType() == "petOwner") {
            return $this->redirectToRoute("gererPetOwner");
        } else {
            return $this->redirectToRoute("gererPetSitter");
        }
        
    }

    #[Route('/bloquer/{id}', name: 'bloquer')]
    public function bloqueUser(ManagerRegistry $doctrine,$id,  Request  $request) : Response
    { $user = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $userType = $doctrine
        ->getRepository(User::class)
        ->find($id);
        $user->setBloque(1);


        $em = $doctrine->getManager();
        $em->flush();

        // envoyer un mail de blocage par l administrateur lui disant que son compte a été créé
        $email = (new Email())    
        ->from('ArtifactPidev@gmail.com')
        ->to($user->getEmail())
        ->subject('Blocage de votre compte')
        ->html('<p> Cher/chère '.$user->getNom().' '.$user->getPrenom().',</p></br>'
        .'<p> Nous vous écrivons pour vous informer que votre compte utilisateur a été bloqué sur notre site. Ce blocage est dû à un non-respect de nos conditions d utilisation ou à des activités suspectes détectées sur votre compte.</p></br>'
        .'<p> Nous vous invitons à prendre contact avec notre service clientèle si vous souhaitez plus d informations sur les raisons de ce blocage ou si vous pensez que cela a été une erreur. </p></br>'
        .'<p> Nous tenons à souligner que la sécurité de notre site et de nos utilisateurs est notre priorité absolue et que nous prenons des mesures pour assurer la sécurité de tous les comptes.</p>'
        .'<p> Nous vous remercions de votre compréhension et restons à votre disposition si vous avez des questions ou des préoccupations.</p>'
        .'<p> Si vous avez des questions ou des commentaires sur l application, n hésitez pas à nous contacter à l adresse e-mail suivante : ArtifactPidev@gmail.com </p></br>'
        .'</br>Cordialement,</br>'
        .'<p> L équipe de Artifact </p>'
        );  
        $transport=(new GmailSmtpTransport('ArtifactPidev@gmail.com','niskijoybvwhekem'));
        $mailer=new Mailer($transport);
        $mailer->send($email);
            
            if ($user->getType() == "veterinaire") {
                return $this->redirectToRoute("gererVet");
            } elseif ($user->getType() == "petOwner") {
                return $this->redirectToRoute("gererPetOwner");
            } else {
                return $this->redirectToRoute("gererPetSitter");
            }      
        
    }

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    

    #[Route('/addUser', name: 'addUser')]
    public function addUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            //tester si il est un vet
            if($user->getType() == "veterinaire"){
                $user->setAcces(0);
            }
            else{
                $user->setAcces(1);
                $user->setBloque(0);
            }

            // Set their role
             // Set their role
             if ($user->getType() == "veterinaire") {
                $user->setRoles(['ROLE_VETERINAIRE']);
            } elseif ($user->getType() == "petOwner") {
                $user->setRoles(['ROLE_PETOWNER']);
            } else {
                $user->setRoles(['ROLE_PETSITTER']);
            }
            

            // envoyer un mail de confirmation par l administrateur lui disant que son compte a été créé
            $email = (new Email())    
            ->from('ArtifactPidev@gmail.com')
            ->to($user->getEmail())
            ->subject('Bienvenue dans notre application')
            ->html('<p> Cher/chère '.$user->getNom().' '.$user->getPrenom().',</p></br>'
            .'<p> Nous sommes ravis de vous accueillir dans notre application ! Nous sommes convaincus que vous allez apprécier les fonctionnalités que nous avons développées pour vous.</p></br>'
            .'<p> Votre compte a été créé avec succès. </p></br>'
            .'<p> Votre email est : '.$user->getEmail().'</p>'
            .'<p> Votre role est : '.$user->getType().'</p>'
            .'<p> Si vous avez des questions ou des commentaires sur l application, n hésitez pas à nous contacter à l adresse e-mail suivante : ArtifactPidev@gmail.com </p></br>'
            .'<p> Encore une fois, bienvenue dans notre application et merci de nous avoir choisi ! </p></br>Cordialement,</br>'
            .'<p> L équipe de Artifact </p>'
            );  
        $transport=(new GmailSmtpTransport('ArtifactPidev@gmail.com','niskijoybvwhekem'));
        $mailer=new Mailer($transport);
        $mailer->send($email);

            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('addUser');
        }
         $error ='';
        // last username entered by the user
        $lastUsername = '';
        return $this->render('admin/addUser.html.twig', [
            'form' => $form->createView(),'last_username' => $lastUsername, 'error' => $error
        ]);
    }

    #[Route('/statistiqueParType', name: 'statistiqueParType')]
    public function statistiqueParType(UserRepository $repo){
        $veterinaire = $repo->findByType("veterinaire");
        $petsitter = $repo->findByType("petSitter");
        $petOwner = $repo->findByType("petOwner");
        return $this->render('admin/statistique.html.twig',[
            'veterinaires' => $veterinaire, 'petsitters' => $petsitter
            ,'petowners' => $petOwner
        ]);
    }

    #[Route('/statistiqueParAdresse', name: 'statistiqueParAdresse')]
    public function statistiqueParAdresse(UserRepository $repo){
        $user = $repo->findByAdresse("Tunis");
        return $this->render('admin/gererPetSitter.html.twig',[
            'users' => $user
        ]);
    }
//-------------------------MOBILE------------------------------
    #[Route('/displayPetSitter', name: 'displayPetSitter')]
    public function displayPetSitter(UserRepository $repo){
        $user = $repo->findByType("petSitter");
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);
    }

    #[Route('/removePetSitterMobile', name: 'removePetSitterMobile')]
    public function removePetSitterMobile(Request $request): Response{
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($id);
        if($user==null){
            $em->remove($user);
            $em->flush();
        }
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize("Utilisateur a ete supprimer avec succes");
        return new JsonResponse($formatted);
        
    }

    #[Route('/updateCompteUserMobile', name: 'updateCompteUserMobile')]
    public function updateCompteUserMobile(Request $request): Response{
        $em = $this->getDoctrine()->getManager();
        $user=$this->getDoctrine()->getManager()->getRepository(User::class)->find($request->get('id'));
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom'));
        $user->setEmail($request->get('email'));
        $user->setAdresse($request->get('adresse'));
        $user->setNumtel($request->get('numtel'));
        $user->setPassword($request->get('password'));
        $em->persist($user);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize("Utilisateur a ete modifier avec succes");
        return new JsonResponse($formatted);
    }
}


