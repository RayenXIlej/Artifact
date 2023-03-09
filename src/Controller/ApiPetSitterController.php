<?php

namespace App\Controller;


use App\Entity\PetSitter;
use App\Entity\Offre;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class   ApiPetSitterController extends AbstractController
{

    #[Route('/api/petsitters', name: 'app_api_petsitters')]
    public function rendezvous(NormalizerInterface $normalizer): Response
    {
        $em = $this->getDoctrine()->getManager();

        $reclamations = $em->getRepository(PetSitter::class)->findAll();
        $jsonContent = $normalizer->normalize($reclamations,'json');
        // $serializer = new Serializer( [new ObjectNormalizer()]);
        return new JsonResponse($jsonContent);

    }




    /*#[Route('/api/addPetSitter', name: 'app_add_petsitter')]
    public function AddPetSitter(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $petsitter = new PetSitter();

        $petsitter ->setNom($request->get('Nom'));
        $petsitter ->setPrenom($request->get('Prenom'));
        $petsitter ->setAdresse($request->get('adresse'));
        $petsitter ->setPhoto($request->get('image'));
        $em->persist($petsitter );
        $em->flush();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formated = $serializer->normalize($petsitter);
        return new JsonResponse($formated);

    }*/

     /**
     * @Route("/newpetsitter_mobile/{nom}/{prenom}{adresse}", name="newpetsitter_mobile", methods={"GET","POST"})
     */
    public function newpetsitter($nom,$prenom,$adresse,NormalizerInterface  $normalizer )
    {

        $petsitter= new Petsitter();
        $petsitter->setNom($nom);
        $petsitter->setPrenom($prenom);
        $petsitter->setAdresse($adresse);
      

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($petsitter);
        $entityManager->flush();
        $json = $normalizer->normalize($petsitter, "json", ['groups' => ['petsitter']]);
        return new JsonResponse($json);


    }
}





    






