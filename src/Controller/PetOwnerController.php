<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PetOwnerController extends AbstractController
{
    #[Route('/petOwner', name: 'app_pet_owner')]
    public function index(): Response
    {
        return $this->render('pet_owner/index.html.twig', [
            'controller_name' => 'PetOwnerController',
        ]);
    }
}
