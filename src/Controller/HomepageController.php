<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\TimeslotsType;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomepageController extends AbstractController
{
    /**
     * @Route("/home", name="homepage")
     */
    public function index(Request $request,RoomRepository $roomRepository, UserInterface $user)
    {
        if($request->request->get('timeslots')){
            $formInput=$request->request->get('timeslots');
            $room=$roomRepository->findOneBy(['name'=> $formInput['room']]);
            $startDateInput=
            $test=$room->canBook($user);
            $test1= $room->isBooked($startDate,$endDate);
        }


        $user=$user->getUsername();
        $rooms=$roomRepository->findAll();
        $form=$this->createForm(TimeslotsType::class);
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'rooms'=>$rooms,
            'form'=>$form->createView(),
            'user'=>$user
        ]);
    }

    public function canBook(){

    }
}
