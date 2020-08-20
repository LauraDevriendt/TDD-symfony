<?php

namespace App\Controller;

use App\Entity\BookingPossible;
use App\Entity\Bookings;
use App\Entity\DateConvertManager;
use App\Entity\Room;
use App\Entity\User;
use App\Exceptions\DateTimeExceptions;
use App\Form\TimeslotsType;
use App\Repository\BookingsRepository;
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
     * @param Request $request
     * @param RoomRepository $roomRepository
     * @param UserInterface $userInterface
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request,RoomRepository $roomRepository, UserInterface $userInterface, UserRepository $userRepository, BookingsRepository $bookingsRepository): \Symfony\Component\HttpFoundation\Response
    {
        $user=$userRepository->findOneBy(['email'=> $userInterface->getUsername()]);
        $rooms=$roomRepository->findAll();
        $form=$this->createForm(TimeslotsType::class);

        if($request->request->get('timeslots')){
            $formInput=$request->request->get('timeslots');
            $startDate=(new DateConvertManager())->convertToDateTimeImmutable($formInput['startdate']);
            $endDate=(new DateConvertManager())->convertToDateTimeImmutable($formInput['enddate']);
            $room=$roomRepository->findOneBy(['id'=> $formInput['room']]);
            $bookingPossible=new BookingPossible($startDate,$endDate,$startDate->diff($endDate),$room,$user);

            if($bookingPossible->isValid()){
                $user->Pay($startDate->diff($endDate));
                $booking=new Bookings($room,$user,$startDate,$endDate);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($booking,$user);
                $entityManager->flush();
                $this->addFlash('success', 'your room is booked');
            }else{
                $this->addFlash('error', 'your room is NOT booked');
            }

        }

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'rooms'=>$rooms,
            'bookings'=>$bookingsRepository->findAll(),
            'form'=>$form->createView(),
            'user'=>$user,
        ]);
    }


}
