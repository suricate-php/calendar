<?php
namespace SuricateCalendar;

class CalendarItem
{
    public $number;


    public function __construct($number = null)
    {
        $this->number = $number;
    }
}