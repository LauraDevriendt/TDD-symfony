<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CreditController extends AbstractController
{
    /**
     * @Route("/credit", name="credit")
     */
    public function index(UserInterface $userInterface, UserRepository $userRepository, Request $request)
    {
        $user=$userRepository->findOneBy(['email'=>$userInterface->getUsername()]);
       if($request->request->get('credit')){
           if($user->canRecharge()){
               $user->setCredit($user->getCredit()+$request->request->get('credit'));
               $this->getDoctrine()->getManager()->flush();
               return $this->redirectToRoute('homepage');
           }else{
               $this->addFlash("error", "Only for premium members");
           }
       }



        return $this->render('credit/index.html.twig', [
            'user' => $user,
        ]);
    }
}
