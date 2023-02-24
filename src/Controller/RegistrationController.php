<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Filesystem\Filesystem;


class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/registration", name="security_registration")
     */
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, SluggerInterface $slugger, UserRepository $repo)
    {
        $user = new User();
        

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pdfFile = $form->get('diplome')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $pdfFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();
        
                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $pdfFilename
                    );
                } catch (FileException $e) {
                    // handle the exception
                }
        
                $user->setDiplome($pdfFilename);
            }
            $repo->save($user,true);
            // Encode the new users password
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            //tester si il est un vet
            if($user->getType() == "veterinaire"){
                $user->setAcces(0);
                
            }
            else{
                $user->setAcces(1);
                
            }
            $user->setBloque(0);
            // Set their role
            if ($user->getType() == "veterinaire") {
                $user->setRoles(['ROLE_VETERINAIRE']);
            } elseif ($user->getType() == "petOwner") {
                $user->setRoles(['ROLE_PETOWNER']);
            } else {
                $user->setRoles(['ROLE_PETSITTER']);
            }
            



            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login', [], 303);
        }
         $error ='';
        // last username entered by the user
        $lastUsername = '';
        return $this->render('registration/inscription.html.twig', [
            'form' => $form->createView(),'last_username' => $lastUsername, 'error' => $error
        ]);
    }
}
