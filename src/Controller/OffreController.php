<?php

namespace App\Controller;



use App\Form\OffreType;


use App\Entity\Offre;


use App\Services\QrcodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Image;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;

class OffreController extends AbstractController
{
    /**
     * @Route("/offre", name="offre")
     */
    public function index(): Response
    {
        return $this->render('offre/index.html.twig', [
            'controller_name' => 'OffreController',
        ]);
    }


 
     
     #[Route('/listOffre', name: 'listOffre', methods: ['GET','POST'])]

   
    public function listOffre(OffreRepository $offreRepository,EntityManagerInterface $entityManager,Request $request ) 
    {
        $offres= $entityManager
        ->getRepository(Offre::class)
        ->findAll();

        /////////
        $back = null;
        
        if($request->isMethod("POST")){
            if ( $request->request->get('optionsRadios')){
                $SortKey = $request->request->get('optionsRadios');
                switch ($SortKey){
                    case 'Description':
                        $offres = $offreRepository->SortByDescription();
                        break;

                    case 'Prix':
                        $offres = $offreRepository->SortByPrix();
                        break;

                    case 'DateDebut':
                        $offres = $offreRepository->SortByDateDebut();
                        break;
                        case 'DateFin':
                            $offres = $offreRepository->SortByDateFin();
                            break;
    


                }
            }
            else
            {
                $type = $request->request->get('optionsearch');
                $value = $request->request->get('Search');
                switch ($type){
                    case 'Description':
                        $offres = $offreRepository->findByDescription($value);
                        break;

                    case 'Prix':
                        $offres = $offreRepository->findByPrix($value);
                        break;

                    case 'DateDebut':
                        $offres = $offreRepository->findByDateDebut($value);
                        break;

                    case 'DateFin':
                        $offres = $offreRepository->findByDateFin($value);
                        break;


                }
            }

            if ( $offres){
                $back = "success";
            }else{
                $back = "failure";
            }
        }
           

    return $this->render('offre/listOffre.html.twig', [
        'listOffres' => $offres,'back'=>$back
    ]);

        
    }


    /**
     * @Route("/addOffre", name="addOffre")
     */
    public function addOffre(Request $request,SluggerInterface $slugger,QrcodeService $qrcodeService,MailerInterface $mailer)
    {
        $offre = new Offre();
        $qrCode=null;

        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && ($form->isValid()))
        {
            $qrCode=$qrcodeService->qrCode($offre->getDescription());
            $email = (new Email())
                ->from('sassi.asma@esprit.tn')
                ->to('sassi.asma@esprit.tn')
                ->subject('Offre!')
                ->text('Offre ajouter!');
            $mailer->send($email);

            $em = $this->getDoctrine()->getManager();
        $em->persist($offre);
        $em->flush();

        return $this->redirectToRoute("showOffre");

    }

        return $this->render('offre/addOffre.html.twig', array("formOffre" => $form->createView()));
    }

    /**
     * @Route("/showOffre", name="showOffre")
     */
    public function showOffre(Request $request)
    {
        $offres = $this->getDoctrine()->getRepository(Offre::class)->findAll();
        $form = $this->createForm(OffreType::class);
        $form->handleRequest($request);

        return $this->render('offre/showOffre.html.twig',
        array("showOffres" => $offres, "formOffre" => $form->createView()));
    }


    /**
     * @Route("/deleteOffre/{id}", name="deleteOffre")
     */
    public function deleteOffre($id)
    {
        $offre = $this->getDoctrine()->getRepository(Offre::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($offre);
        $em->flush();
        return $this->redirectToRoute("showOffre");
    }

    /**
     * @Route("/updateOffre/{id}", name="updateOffre")
     */
    public function updateOffre(Request $request, $id,SluggerInterface $slugger)
    {
        $offre = $this->getDoctrine()->getRepository(Offre::class)->find($id);
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && ($form->isValid())) {



            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("showOffre");
        }

        return $this->render('offre/updateOffre.html.twig', array("formOffre" => $form->createView()));

    }

}




