<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $onlyForPremiumMembers;

    /**
     * @ORM\OneToMany(targetEntity=Bookings::class, mappedBy="room")
     */
    private $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOnlyForPremiumMembers(): ?bool
    {
        return $this->onlyForPremiumMembers;
    }

    public function setOnlyForPremiumMembers(bool $onlyForPremiumMembers): self
    {
        $this->onlyForPremiumMembers = $onlyForPremiumMembers;

        return $this;
    }

    /**
     * @return Collection|Bookings[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Bookings $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setRoom($this);
        }

        return $this;
    }

    public function removeBooking(Bookings $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getRoom() === $this) {
                $booking->setRoom(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function canBook(User $user): bool
    {
        return (
            !$this->getOnlyForPremiumMembers() ||
            $user->getPremiumMember() === $this->getOnlyForPremiumMembers());
    }

    public function isNotBooked(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): bool
    {
        if (!empty($this->getBookings()->toArray())) {
            $bookings = $this->getBookings()->toArray();
            /**
             * @var Bookings[] $bookings
             */
            foreach ($bookings as $booking) {

                if ((($startDate >= $booking->getStartdate()) && ($startDate <= $booking->getEnddate()))
                    || (($endDate >= $booking->getStartdate()) && ($endDate <= $booking->getEnddate()))) {
                    return false;

                }
            }
            return true;
        }
        return true;
    }

    public function checkMeetingDuration(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): bool
    {
        $minutesInHour=60;
        $hourLimit=4;
        $durationInMinutes=($endDate->getTimestamp() - $startDate->getTimestamp()) / $minutesInHour;
        if($durationInMinutes>$minutesInHour*$hourLimit){
            return false;
        }

        return true;


    }

}
