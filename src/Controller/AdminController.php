<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

            $user->setRoles(['ROLE_USER']);

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


