<?php

namespace App\Tests;

use App\Entity\Bookings;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\BookingsRepository;
use DateInterval;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Date;

// Rooms marked as premium can only be hired for premium members
final class RoomAvailabilityTest extends TestCase
{
    public function dataProviderForPremiumRoom() : array
    {
        return [
            [true, true, true],
            [false, false, true],
            [false, true, true],
            [true, false, false]
        ];
    }
    /**
     * @dataProvider dataProviderForPremiumRoom
     */
    public function testPremiumRoom(bool $roomVar, bool $userVar, bool $expectedOutput): void
    {
        $room = (new Room)->setOnlyForPremiumMembers($roomVar);
        $user = (new User)->setPremiumMember($userVar);

        self::assertEquals($expectedOutput, $room->canBook($user));
    }

    public function dataProviderForIsBooked() : array
    {
        $beginBooking= new \DateTimeImmutable();
        $endBooking=$beginBooking->add(new DateInterval('P0Y0M0DT2H0M0S'));

        $startDate= $beginBooking->add(new DateInterval('P0Y0M0DT4H0M0S'));
        $endDate= $startDate->add(new DateInterval('P0Y0M0DT2H0M0S'));

        $badStartDate= $beginBooking->add(new DateInterval('P0Y0M0DT0H30M0S'));
        $badEndDate=$beginBooking->add(new DateInterval('P0Y0M0DT1H0M0S'));

        $room= new Room();
        $user=new User();

        $room->addBooking(new Bookings( $room, $user,$beginBooking, $endBooking));
        return [
            [new Room(), $startDate,$endDate,false],
            [ $room,$startDate, $endDate, false],
            [ $room,$beginBooking, $badEndDate, true],
            [ $room,$badStartDate, $endDate, true],
            [ $room,$badStartDate, $badEndDate, true],
        ];
    }
    /**
     * @dataProvider dataProviderForIsBooked
     */
    // Room can only be booked if no other User has already booked it in the framework
    public function testRoomOccupied(Room $room,\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, bool $expected){

        self::assertEquals($expected, $room->isBooked($startDate,$endDate));

    }

    public function dataProviderForMeetingDuration() : array
    {
        $beginBooking= new \DateTimeImmutable();
        $endBooking=$beginBooking->add(new DateInterval('P0Y0M0DT4H0M0S'));
        $badEndBookig= $beginBooking->add(new DateInterval('P0Y0M0DT4H30M0S'));

        return [
            [$beginBooking, $badEndBookig, true],
            [$beginBooking, $endBooking, false],

        ];
    }
    /**
     * @dataProvider dataProviderForMeetingDuration
     */
    //No room can be book for more than 4 hours
    public function testMeetingDurationLongerThan4Hours(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, bool $expected): void
    {
        self::assertEquals($expected, (new Room())->checkMeetingDuration($startDate,$endDate));
    }

    public function dataProviderForUserCredit() : array
    {
       $user1=(new User())->setCredit(100);
       $user2=(new User())->setCredit(2);
       $duration=new DateInterval('P0Y0M0DT4H0M0S');
       $durationWithMinutes=new DateInterval('P0Y0M0DT1H30M0S');
        return [
            [$user1, $duration ,true],
            [$user2,$duration, false],
            [$user2,$durationWithMinutes, false],
            [$user1,$durationWithMinutes, true],

        ];
    }

    /**
     * @dataProvider dataProviderForUserCredit
     */
    // Check if they can afford the rent for the room
    public function testCanUserPay(User $user, \DateInterval $time, bool $expected):void{
        self::assertEquals($expected, $user->canPay($time));
    }

}
