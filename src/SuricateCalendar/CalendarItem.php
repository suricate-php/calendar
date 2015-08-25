<?php
namespace SuricateCalendar;

class CalendarItem
{
    public $number;
    public $past    = false;
    public $future  = false;
    public $items   = [];


    public function __construct($number = null)
    {
        $this->number = $number;
    }
}