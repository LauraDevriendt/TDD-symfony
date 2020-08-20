<?php

namespace App\Form;

use App\Entity\Bookings;
use App\Entity\Room;
use App\Repository\RoomRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeslotsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $start = \DateTime::createFromFormat('H:i', '09:00');
        $end = \DateTime::createFromFormat('H:i', '17:00');
        $now = new \DateTime();

        if($now < $start) {
            $startDate = $start;
            $endDate = $end;
        } elseif ($now >= $start && $now <= $end) {
            $startDate = $now;
            $endDate = $end;
        } elseif ($now > $end) {
            $startDate = $start->add(new \DateInterval('P1D'));
            $endDate = $end->add(new \DateInterval('P1D'));
        }

        $builder
            ->add('startdate', DateTimeType::class, [
                'date_label' => 'Starts at',
                'data'=> $startDate,
                'years'=> range($now->format('Y'),(new \DateTime())->add(new \DateInterval('P5Y'))->format('Y')),
                'hours'=> range($startDate->format('H'),$endDate->format('H')),
                'minutes'=>[0,15,30,45]
            ])
            ->add('enddate', DateTimeType::class, [
                'date_label' => 'Ends at',
                'data'=> $endDate,
                'years'=> range($now->format('Y'),(new \DateTime())->add(new \DateInterval('P5Y'))->format('Y')),
                'hours'=> range($startDate->format('H'),$endDate->format('H')),
                'minutes'=>[0,15,30,45]
            ])
            ->add('room',EntityType::class, [
                'class' => Room::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'name',
            ])
            /*
             * ChoiceType::class, [
                'choices'=> function() use ($roomRepository) {
                $roomNames=[];
                foreach ($roomRepository->findAll() as $room){
                    $roomNames[]="{$room->getName()}=>{$room->getName()}";
                }
                return$roomNames;
                },
                'choice_value' => 'name',
    ])
             */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bookings::class,
        ]);
    }
}
