<?php

namespace App\Controller;


namespace App\Controller;


use App\Form\PetSitterType;


use App\Entity\PetSitter;


use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Image;


class PetSitterController extends AbstractController
{ 
    /**
     * @Route("/petsitter", name="petsitter")
     */
    public function index(): Response
    {
        
        return $this->render('petsitter/addPetSitter.html.twig', [
            'controller_name' => 'PetSitterController',
        ]);
    }


    /**
     * @Route("/listPetSitter", name="listPetSitter")
     */
    public function listPetSitter()
    {
        $petsitters = $this->getDoctrine()->getRepository(PetSitter::class)->findAll();
        return $this->render('petsitter/listPetSitter.html.twig', array("listPetSitters" => $petsitters));
    }


    /**
     * @Route("/addPetSitter", name="addPetSitter")
     */
    public function addPetSitter(Request $request,SluggerInterface $slugger)
    {
        $petsitter = new PetSitter();
        $form = $this->createForm(PetSitterType::class, $petsitter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && ($form->isValid()))

        {
            $images = $form->get('Photo')->getData();
            if($images) {
                $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $ficher  = $safeFilename.'-'.uniqid().'.'.$images->guessExtension();

                $images->move(
                    $this->getParameter('image_directory'), $ficher
                );
                $petsitter->setPhoto($ficher);
            }
            $em = $this->getDoctrine()->getManager();
        $em->persist($petsitter);
        $em->flush();

        return $this->redirectToRoute("showPetSitter");

    }

        return $this->render('petsitter/addPetSitter.html.twig', array("formPetSitter" => $form->createView()));
    }

    /**
     * @Route("/showPetSitter", name="showPetSitter")
     */
    public function showPetSitter(Request $request)
    {
        $petsitters = $this->getDoctrine()->getRepository(PetSitter::class)->findAll();
        $form = $this->createForm(PetSitterType::class);
        $form->handleRequest($request);
        return $this->render('petsitter/showPetSitter.html.twig',
         array("showPetSitters" => $petsitters, "formPetSitter" => $form->createView()));
    }


    /**
     * @Route("/deletePetSitter/{id}", name="deletePetSitter")
     */
    public function deletePetSitter($id)
    {
        $petsitter = $this->getDoctrine()->getRepository(PetSitter::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($petsitter);
        $em->flush();
        return $this->redirectToRoute("showPetSitter");
    }

    /**
     * @Route("/updatePetSitter/{id}", name="updatePetSitter")
     */
    public function updatePetSitter(Request $request, $id,SluggerInterface $slugger)
    {
        $petsitter = $this->getDoctrine()->getRepository(PetSitter::class)->find($id);
        $form = $this->createForm(PetSitterType::class, $petsitter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && ($form->isValid())) {

                $images = $form->get('Photo')->getData();
                if($images) {
                    $originalFilename = pathinfo($images->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $ficher  = $safeFilename.'-'.uniqid().'.'.$images->guessExtension();

                    $images->move(
                        $this->getParameter('image_directory'), $ficher
                    );
                    $petsitter->setPhoto($ficher);
                }


            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("showPetSitter");
        }

        return $this->render('petsitter/updatePetSitter.html.twig', array("formPetSitter" => $form->createView()));

    }
    
    /**
     * @Route("/addSitter", name="addSitter")
     */
    public function addSitter(Request $request ,SluggerInterface $slugger)
    {
        $petsitter = new PetSitter();
        $form = $this->createForm(PetSitterType::class, $petsitter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && ($form->isValid()))

        {
            $imagess = $form->get('Photo')->getData();
            if($imagess) {
                $originalFilename = pathinfo($imagess->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $ficher  = $safeFilename.'-'.uniqid().'.'.$imagess->guessExtension();

                $imagess->move(
                    $this->getParameter('img_directory'), $ficher
                );
                $petsitter->setPhoto($ficher);
            }
            $em = $this->getDoctrine()->getManager();
        $em->persist($petsitter);
        $em->flush();
        
        return $this->redirectToRoute("app_admin");
    }
       
        
        return $this->render('petsitter/petsitterForm.html.twig', array("formPetSitter" => $form->createView()));
    }


    }

