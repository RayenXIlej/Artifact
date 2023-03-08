<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetPasswordType;
use App\Form\ForgetPasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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
            


            // envoyer un mail de confirmation
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

            return $this->redirectToRoute('app_login', [], 303);
        }
         $error ='';
        // last username entered by the user
        $lastUsername = '';
        return $this->render('registration/inscription.html.twig', [
            'form' => $form->createView(),'last_username' => $lastUsername, 'error' => $error
        ]);
    }


    #[Route('/forget_password', name: 'forget_password')]
    public function forgetPassword(Request $request,UserRepository $repo): Response
    {
     
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           $email = $form['email']->getData();
           $test = $repo->findOneByEmail($email);
           if($test != null){
            $url="http://localhost:8000/reset_password/".$test->getId();
            $email = (new Email())
            ->from('ArtifactPidev@gmail.com')
            ->to($email)
            ->subject('MYVET')
            ->html("cher <b>" .$test->getNom(). "</b> Nous avons reçu une demande de réinitialisation de votre mot de passe pour notre application.<br> Veuillez cliquer sur le lien suivant <a href=$url> ici </a><br> Cordialement<br><b> Administration MyVet</b> ");
            $transport=(new GmailSmtpTransport('ArtifactPidev@gmail.com','niskijoybvwhekem'));
             $mailer=new Mailer($transport);
            $mailer->send($email);
            $this->addFlash('success','demande réinitialisation est effectué avec succés');

          }
          else{
            $this->addFlash('danger','cette adresse n\'existe pas');
        }
        }
        return $this->render('security/forgetPassword.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/reset_password/{id}', name: 'reset_password')]
    public function reset($id,Request $request,UserRepository $repo,UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           $test = $repo->findOneById($id);
             if($test != null){
                $plaintextPassword=$form["password"]->getData();
                // hash the password (based on the security.yaml config for the $user class)
               $hashedPassword = $passwordHasher->hashPassword(
               $test,
               $plaintextPassword
               );
                $test->setPassword($hashedPassword);
                $em = $this->getDoctrine()->getManager();//doctrine
                $em->flush();
             $this->addFlash('success','mot de passe est rénietialisé');
             return $this->redirectToRoute('app_login');

             }
        }
        return $this->render('security/resetPassword.html.twig',[
            'form'=>$form->createView(),
        ]);
    
}

    //--------------------MOBILE------------------------

    /**
     * @Route("/registrationMobile", name="security_registrationMobile")
     */
    public function registrationMobile(Request $request, UserPasswordEncoderInterface $encoder)
    {
        
        $email=$request->query->get('email');
        //$username=$request->query->get('username');
        $password=$request->query->get('password');
        $roles=$request->query->get('roles');
        $nom=$request->query->get('nom');
        $prenom=$request->query->get('prenom');
        $adresse=$request->query->get('adresse');
        $type=$request->query->get('type');
        //$acces=$request->query->get('acces');
        //$bloque=$request->query->get('bloque');
        $numtel=$request->query->get('numtel');

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return new JsonResponse("email ghalet");
        }

        $user = new User();
        
        
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        // $user->SetAcces(1);
        // $user->SetBloque(0);

        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setAdresse($adresse);
        $user->setType($type);
        $user->setRoles(array($roles));
        $user->setNumtel($numtel);
        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("Account is created",200);//200 http resultat server ok
        }
        catch(\Exception $e){
            return new JsonResponse("exception".$e->getMessage());//500 http resultat server error
        }
        //$user->setAcces($acces);
        //$user->setBloque($bloque);
             
    }


    #[Route(path: '/loginMobile', name: 'app_loginMobile')]
    public function loginMobile(Request $request)
    {
        $email=$request->query->get('email');
        $password=$request->query->get('password');
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user){
            if(password_verify($password,$user->getPassword())){
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
            else{
                return new JsonResponse("password is incorrect");
            }
        }
        else{
            return new JsonResponse("email is incorrect");
        } 
    }

    #[Route('/updateCompteMobile', name: 'updateCompteMobile')]
    public function updateCompteMobile(Request $request, UserPasswordEncoderInterface $encoder){
        $id=$request->query->get('id');
        $email=$request->query->get('email');
        $password=$request->query->get('password');
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($id);


        
        $user->setEmail($request->get('email'));
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password)); 
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom'));
        $user->setAdresse($request->get('adresse'));
        $user->setNumtel($request->get('numtel'));
        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("Account is updated",200);//200 http resultat server ok
        }
        catch(\Exception $e){
            return new JsonResponse("fail".$e->getMessage());//500 http resultat server error
        }

       
    }

}
