<?php


namespace App\Entity;


class BookingPossible
{
    private bool $valid = false;
    private \DateTimeImmutable $startDate;
    private \DateTimeImmutable $endDate;
    private \DateInterval $interval;
    private Room $room;
    private User $user;

    /**
     * BookingPossible constructor.
     * @param bool $valid
     * @param \DateTimeImmutable $startDate
     * @param \DateTimeImmutable $endDate
     * @param \DateInterval $interval
     * @param Room $room
     * @param User $user
     */
    public function __construct(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, \DateInterval $interval, Room $room, User $user)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->interval = $interval;
        $this->room = $room;
        $this->user = $user;

        if($this->dateCheck() && $this->canBookRoom() && $this->canUserBook()){
            $this->valid=true;
        }
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    public function dateCheck(): ?bool
    {
        if ($this->startDate < $this->endDate || $this->startDate != $this->endDate || $this->startDate< new \DateTime()) {
           return true;
        }
    }

    private function canUserBook(): ?bool
    {
        if ($this->user->canPay($this->interval)) {
       return true;
    }
    }

    private function canBookRoom(): ?bool
    {
        if (
        $this->room->canBook($this->user) &&
        $this->room->isNotBooked($this->startDate, $this->endDate) &&
        $this->room->checkMeetingDuration($this->startDate, $this->endDate)
    ){
        return true;
    }

    }
}