<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Repository\FiliereRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/filieres', name: 'api_entreprise_')]  
class FiliereController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,private FiliereRepository $filiereRepository,private SerializerInterface $jmsSerializer)
    {}

    #[Route('/add', name: 'add')]
    public function register(Request $request): JsonResponse
    {
       $modules = $request->get('modules');
       $name = $request->get('name');

       $filiere = new Filiere();
       $filiere->setName($name);
       $filiere->setModules($modules);
       
       $this->em->persist($filiere);
       $this->em->flush();
 
       return $this->json(['message' => 'Enseignat Registered Successfully']);
        } 
 
        #[Route('/', name: 'all',methods: ['GET'])]
        public function index(){
            $etudiants = $this->filiereRepository->findAll();
            $json = $this->jmsSerializer->serialize($etudiants, 'json', SerializationContext::create()->setGroups(array('filiere')));
            return new JsonResponse($json, 200, [], true);
        }
}
