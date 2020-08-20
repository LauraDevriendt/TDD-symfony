<?php


namespace App\Entity;


class DateConvertManager
{
    public function convertToDateTimeImmutable(array $date):\DateTimeImmutable{
        $dateString=strtotime($date['date']['year']."-".$date['date']['month']."-".$date['date']['day']." ".$date['time']['hour'].":".$date['time']['minute']);
        return new \DateTimeImmutable(date('Y-m-d H:i:s', $dateString));
    }
}