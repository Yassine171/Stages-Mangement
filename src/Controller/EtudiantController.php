<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

 #[Route('/api/etudiants', name: 'api_etudiant_')]  
class EtudiantController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em,private UserPasswordHasherInterface $passwordHasher,private EtudiantRepository $etudiantRepository,private SerializerInterface $jmsSerializer)
    {

     } 

    #[Route('/register', name: 'register')]
    public function register(Request $request,SluggerInterface $slugger,FiliereRepository $filiereRepository): JsonResponse
    {
        //$decoded = json_decode($request->getContent());
        // dd($request);
        $email = $request->get('email');
        $plaintextPassword = $request->get('password');
        $name = $request->get('name');
        $nvScolaire = $request->get('nvScolaire');
        //$cv = $request->get('cv');
        $filiere = $request->get('filiere');
       $cv = $request->files->get('cv');
        $etudiant = new Etudiant();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $etudiant,
            $plaintextPassword
        );
        $etudiant->setPassword($hashedPassword);
        $etudiant->setName($name);
        $etudiant->setEmail($email);
        $etudiant->setNvScolaire($nvScolaire);
       // dd($filiereRepository->findById($filiere));
       $filiere=$filiereRepository->findOneById($filiere);
        $etudiant->setFiliere($filiere);

        //dd($cv);
        if ($cv) {
            $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();
            $fileName = md5(uniqid()).'.'.$cv->guessExtension();
            $cv->move(
                $this->getParameter('cv_directory'),
                $newFilename
            );
            $etudiant->setCvName('https://localhost:8000/cv/'.$newFilename);
        }

        $this->em->persist($etudiant);
        $this->em->flush();
  
        return $this->json(['message' => 'Etudiant Registered Successfully']);
    }

    #[Route('/', name: 'all',methods: ['GET'])]
    public function index(){
        $etudiants = $this->etudiantRepository->findAll();
        $json = $this->jmsSerializer->serialize($etudiants, 'json', SerializationContext::create()->setGroups(array('etudiant')));
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'get',methods: ['GET'])]
    public function show(Etudiant $etudiant){

        $json = $this->jmsSerializer->serialize($etudiant, 'json', SerializationContext::create()->setGroups(array('etudiant')));
        return new JsonResponse($json, 200, [], true);
       // return $this->json($etudiant);
    }

    #[Route('/update/{id}', name: 'update',methods: ['POST','PUT'])]
    public function update(Etudiant $etudiant, Request $request,SluggerInterface $slugger)
    {
    
        //$data = json_decode($request->getContent(), true);
       //dd($request->request->all(),$request->files->get('cv'));

        $form = $this->createForm(EtudiantType::class, $etudiant,array('csrf_protection' => false));
        //dump($form->getData());

         $form->submit($request->request->all(),false);
         $cv=$request->files->get('cv');
        // dump($form->getData());
        //  foreach ($form->getErrors() as $error) {
        //     // do something with the error
        //     dump($error->getMessage(),$error->getOrigin()->getName());
        // };
   
        if ($form->isValid()) {
            if($cv){
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();
                $fileName = md5(uniqid()).'.'.$cv->guessExtension();
                $cv->move(
                    $this->getParameter('cv_directory'),
                    $newFilename
                );
                $oldFile = 'cv/'.basename($etudiant->getCvName());
                if(file_exists($oldFile)){
                    unlink($oldFile);
                }
                $etudiant->setCvName('https://localhost:8000/cv/'.$newFilename);
            }
             $this->etudiantRepository->save($etudiant,true);
             $context = new SerializationContext();
             $context->setGroups(['etudiant']);
             $json = $this->jmsSerializer->serialize($etudiant, 'json', SerializationContext::create()->setGroups(array('etudiant')));
             return new JsonResponse($json, 200, [], true);
         }
         return new JsonResponse($form->getErrors(), 400);
        }

        #[Route('/{id}', name: 'delete',methods: ['DELETE'])]
        public function delete(Etudiant $etudiant){
           $this->etudiantRepository->remove($etudiant,true);
           return $this->json([
            'message' => 'Deleted Succefully',
            'status' => 200,
        ]);
       }
    
    }
