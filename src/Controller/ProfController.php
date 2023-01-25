<?php

namespace App\Controller;

use App\Entity\Prof;
use App\Form\ProfType;
use App\Repository\ProfRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api/profs', name: 'api_prof_')]  
class ProfController extends AbstractController
{
  
    public function __construct(private EntityManagerInterface $em,private UserPasswordHasherInterface $passwordHasher,private ProfRepository $profRepository,private SerializerInterface $jmsSerializer)
    {

     } 

     #[Route('/register', name: 'register')]
     public function register(Request $request,SluggerInterface $slugger): JsonResponse
     {
        $email = $request->get('email');
        $plaintextPassword = $request->get('password');
        $name = $request->get('name');
        $modules = $request->get('modules');

        $prof = new Prof();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $prof,
            $plaintextPassword
        );
        $prof->setPassword($hashedPassword);
        $prof->setName($name);
        $prof->setEmail($email);
        $prof->setModules($modules);
        
        $this->em->persist($prof);
        $this->em->flush();
  
        return $this->json(['message' => 'Enseignat Registered Successfully']);
     }

     #[Route('/', name: 'all',methods: ['GET'])]
     public function index(){
         $profs = $this->profRepository->findAll();
         $json = $this->jmsSerializer->serialize($profs, 'json', SerializationContext::create()->setGroups(array('prof')));
         return new JsonResponse($json, 200, [], true);
     }

     #[Route('/{id}', name: 'get',methods: ['GET'],requirements: ['id' => '\d+'])]
     public function show(Prof $prof){
 
         $json = $this->jmsSerializer->serialize($prof, 'json', SerializationContext::create()->setGroups(array('prof')));
         return new JsonResponse($json, 200, [], true);
        // return $this->json($etudiant);
     }

     #[Route('/{email}', name: 'getByEmail',methods: ['GET'])]
     public function showByEmail(String $email){
        $prof =$this->profRepository->findOneByEmail($email);
         $json = $this->jmsSerializer->serialize($prof, 'json', SerializationContext::create()->setGroups(array('prof')));
         return new JsonResponse($json, 200, [], true);
        // return $this->json($etudiant);
     }

     #[Route('/update/{id}', name: 'update',methods: ['POST','PUT'])]
     public function update(Prof $prof, Request $request,SluggerInterface $slugger)
     {
     
         //$data = json_decode($request->getContent(), true);
        //dd($request->request->all(),$request->files->get('cv'));
 
         $form = $this->createForm(ProfType::class, $prof,array('csrf_protection' => false));
         //dump($form->getData());
 
         //dump($request->get('modules'));
         //array_push($prof->getModules(),$request->request->all()['modules']);
         $form->submit($request->request->all(),false);
         //dump($request->getContent());
        //   foreach ($form->getErrors() as $error) {
        //      // do something with the error
        //      dump($error->getMessage(),$error->getOrigin()->getName());
        //  };
    
         if ($form->isValid()) {
             
              $this->profRepository->save($prof,true);
              $context = new SerializationContext();
              $context->setGroups(['prof']);
              $json = $this->jmsSerializer->serialize($prof, 'json', SerializationContext::create()->setGroups(array('prof')));
              return new JsonResponse($json, 200, [], true);
          }
          return new JsonResponse($form->getErrors(), 400);
         }

         #[Route('/{id}', name: 'delete',methods: ['DELETE'])]
         public function delete(Prof $prof){
            $this->profRepository->remove($prof,true);
            return $this->json([
             'message' => 'Deleted Succefully',
             'status' => 200,
         ]);
        }
}
