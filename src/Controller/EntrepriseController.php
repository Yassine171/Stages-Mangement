<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api/entreprises', name: 'api_entreprise_')]  
class EntrepriseController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em,private UserPasswordHasherInterface $passwordHasher,private EntrepriseRepository $entrepriseRepository,private SerializerInterface $jmsSerializer)
    {}



 

     #[Route('/register', name: 'register')]
     public function register(Request $request): JsonResponse
     {
        $email = $request->get('email');
        $plaintextPassword = $request->get('password');
        $name = $request->get('name');

        $entreprise = new Entreprise();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $entreprise,
            $plaintextPassword
        );
        $entreprise->setPassword($hashedPassword);
        $entreprise->setName($name);
        $entreprise->setEmail($email);

        $this->em->persist($entreprise);
        $this->em->flush();
  
        return $this->json(['message' => 'Enseignat Registered Successfully']);
     }



     #[Route('/{id}', name: 'get',methods: ['GET'],requirements: ['id' => '\d+'])]
     public function show(Entreprise $entreprise){
 
         $json = $this->jmsSerializer->serialize($entreprise, 'json', SerializationContext::create()->setGroups(array('entreprise')));
         return new JsonResponse($json, 200, [], true);
        // return $this->json($etudiant);
     }

     #[Route('/{email}', name: 'getByEmail',methods: ['GET'])]
     public function showByEmail(String $email){
        $prof =$this->entrepriseRepository->findOneByEmail($email);
         $json = $this->jmsSerializer->serialize($prof, 'json', SerializationContext::create()->setGroups(array('entreprise')));
         return new JsonResponse($json, 200, [], true);
        // return $this->json($etudiant);
     }

     #[Route('/', name: 'get_all',methods: ['GET'])]
     public function index(){

         $entreprises = $this->entrepriseRepository->findAll();
         $json = $this->jmsSerializer->serialize($entreprises, 'json', SerializationContext::create()->setGroups(array('entreprise')));
         return new JsonResponse($json, 200, [], true);
     }

     #[Route('/update/{id}', name: 'update',methods: ['POST','PUT'])]
     public function update(Entreprise $entreprise, Request $request)
     {
     
         //$data = json_decode($request->getContent(), true);
        //dd($request->request->all(),$request->files->get('cv'));
 
         $form = $this->createForm(EntrepriseType::class, $entreprise,array('csrf_protection' => false));
         //dump($form->getData());
 
         //dump($request->get('modules'));
         //array_push($entreprise->getModules(),$request->request->all()['modules']);
         $form->submit($request->request->all(),false);
         //dump($request->getContent());
        //   foreach ($form->getErrors() as $error) {
        //      // do something with the error
        //      dump($error->getMessage(),$error->getOrigin()->getName());
        //  };
    
         if ($form->isValid()) {
             
              $this->entrepriseRepository->save($entreprise,true);
              $context = new SerializationContext();
              $context->setGroups(['entreprise']);
              $json = $this->jmsSerializer->serialize($entreprise, 'json', SerializationContext::create()->setGroups(array('entreprise')));
              return new JsonResponse($json, 200, [], true);
          }
          return new JsonResponse($form->getErrors(), 400);
         }

         #[Route('/{id}', name: 'delete',methods: ['DELETE'])]
         public function delete(Entreprise $entreprise){
            $this->entrepriseRepository->remove($entreprise,true);
            return $this->json([
             'message' => 'Deleted Succefully',
             'status' => 200,
         ]);
        }

}
