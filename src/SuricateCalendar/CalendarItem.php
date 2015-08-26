<?php
namespace SuricateCalendar;

class CalendarItem
{
    public $number;
    public $isDay           = false;
    public $isHour          = false;
    public $isPast          = false;
    public $isFuture        = false;
    public $isActive        = false;
    public $showItems       = false;
    public $showItemsNb     = false;
    public $items           = [];


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
            $classes[] = 'has-items';
        }

        if ($this->isActive) {
            $classes[] = 'today';
        }
        $cellClass = ' class="' . implode(' ', $classes) . '"';
       
        $output  = '<td' . $cellClass . '>';
        $output .= '    <div class="number">' . $this->number . '</div>';
        if ($this->showItemsNb) {
            $output .= '<div class="nb">' . count($this->items)  . '</div>';
        } elseif ($this->showItems) {
            $output .= '<ul class="items">';
            foreach ($this->items as $item) {
                $output .= '<li>' . $item . '</li>';
            }
            $output .= '</ul>';
        }
        $output .= '</td>';

        return $output;
    }
}