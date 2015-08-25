<?php
namespace SuricateCalendar;

class CalendarItem
{
    public $number;
    public $isDay     = false;
    public $isHour    = false;
    public $isPast    = false;
    public $isFuture  = false;
    public $isActive  = false;
    public $items   = [];


    public function __construct($number = null)
    {
        $this->number = $number;
    }

    public function render()
    {
        $classes = ['day'];

        if ($this->isFuture) {
            $classes[] = 'future';
        } elseif ($this->isPast) {
            $classes[] = 'past';
        }
        if (count($this->items)) {
            $classes[] = 'has-item';
        }

        if ($this->isActive) {
            $classes[] = 'active';
        }
        $cellClass = ' class="' . implode(' ', $classes) . '"';
       
        $output  = '<td' . $cellClass . '>';
        $output .= '    ' . $this->number;
        $output .= '</td>';

        return $output;
    }
}