<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PetOwnerController extends AbstractController
{
    #[Route('/petOwner', name: 'app_pet_owner')]
    public function index(): Response
    {
        return $this->render('pet_owner/index.html.twig', [
            'controller_name' => 'PetOwnerController',
        ]);
    }

    #[Route('/petOwner/about', name: 'aboutPetOwner')]
    public function about(): Response
    {
        return $this->render('pet_owner/about.html.twig', [
            'controller_name' => 'PetOwnerController',
        ]);
    }

    #[Route('/petOwner/updateCompte', name: 'updatePetOwner')]
    public function editProfil(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
 
        $user = $this->getUser();
 
        $form = $this->createForm(UserType::class, $user);
 
        $form->handleRequest($request);
 
        if($form->isSubmitted() && $form->isValid()){
 
            
 
            $newPassword = $form->get('password')->getData();
 
            // Grâce au service, on génère un nouveau hash de notre nouveau mot de passe
            $hashOfNewPassword = $encoder->encodePassword($user, $newPassword);
 
            // On change l'ancien mot de passe hashé par le nouveau que l'on a généré juste au dessus
            $user->setPassword( $hashOfNewPassword );

            $em = $this->getDoctrine()->getManager();
 
            $em->flush();
 
            $this->addFlash('success', 'Profil modifié avec succès.');
            return $this->redirectToRoute('updatePetOwner');
            echo"<script type='text/javascript'>
                    setTimeout(function () { window.location.href = window.location.href; }, 5000);
            </script>";
        }
 
        // Pour que la vue puisse afficher le formulaire, on doit lui envoyer le formulaire généré, avec $form->createView()
        return $this->render('pet_owner/userUpdate.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
