<?php

namespace SuricateCalendar;

/**
 * @property string locale
 * @property int displayMode
 * @property boolean showDays
 * @property boolean showMonth
 */
class Calendar
{
    const DAY_MODE          = 'day';
    const WEEK_MODE         = 'week';
    const MONTH_MODE        = 'month';

    protected $container                = [];

    protected $displayDays  = false;
    protected $calendarId   = 1;
    protected $currentDate;
    protected $currentTimestamp;
    protected $day;
    protected $month;
    protected $year;
    protected $params       = [];
    
    public function __construct($date = null, $params = [])
    {
        $defaultParams = [
            'id'            => 1,
            'locale'        => 'fr_FR.UTF8',
            'displayMode'   => self::MONTH_MODE,

            // Month params
            'showDays'      => true,
            'showMonth'     => true,


            // week params

            // day params

            // merged params
            'showFullDayName' => true,
            
            'weekStartOn'   => 'sunday',
            ];

        if ($date === null) {
            $date = date('Ymd');
        }
        $this->currentDate      = $date;
        $this->currentTimestamp = strtotime($date);
        $this->year             = strftime('%Y', $this->currentTimestamp);
        $this->month            = strftime('%m', $this->currentTimestamp);
        $this->day              = strftime('%d', $this->currentTimestamp);

        $this->params = array_merge($defaultParams, $params);

        $this->initContainer();
    }

    public function __get($var)
    {
        if (array_key_exists($var, $this->params)) {
            return $this->params[$var];
        } else {
            return null;
        }
    }

    private function initContainer()
    {
        if ($this->displayMode == self::DAY_MODE) {
            $this->initDayContainer();
        } elseif ($this->displayMode == self::WEEK_MODE) {
            $this->initWeekContainer();
        } else {
            $this->initMonthContainer();
        }
    }

    private function initDayContainer()
    {

    }

    private function initWeekContainer()
    {

    }

    private function initMonthContainer()
    {
        $firstDayOfMonth = mktime(0,0,0, $this->month, 1, $this->year);
        $firstDayOffset  = date('w', $firstDayOfMonth);

        $nbDaysInMonth   = date('t', $this->currentTimestamp);
        $nbDays          = $nbDaysInMonth;

        if ($this->weekStartOn == 'monday') {
            $firstDayOffset = ($firstDayOffset == 0) ? 7 : $firstDayOffset;
        }
        $startOffset    = -1 * ($firstDayOffset - 1);
        $endOffset      = 7 - (abs($nbDaysInMonth - $startOffset) % 7);
        
        for ($i = $startOffset; $i < $nbDays + $endOffset; $i++) {
            $ts = strtotime($i . ' days', $firstDayOfMonth);
            $dayIdentifier = date('Ymd', $ts);
            $item = new CalendarItem(date('d', $ts));
            $item->isDay = true;
            if ($dayIdentifier == $this->currentDate) {
                $item->isActive = true;
            }
            if ($i < 0) {
                $item->isPast = true;
            } elseif ($i >= $nbDaysInMonth) {
                $item->isFuture = true;
            }

            $this->container[$dayIdentifier] = $item;
        }
    }

    public function render()
    {
        $output = '';

        $oldLocale = setlocale(LC_TIME, 0);
        
        if (!setlocale(LC_TIME, $this->locale)) {
            throw new \InvalidArgumentException("Missing locale on system : " . $this->locale);
        }

        $output = '<table border="1" class="calendar" id="calendar-' . $this->id . '">';

        switch ($this->displayMode) {
            case self::DAY_MODE:
                $output .= $this->renderDay();
                break;
            case self::WEEK_MODE:
                $output .= $this->renderWeek();
                break;
            default:
                $output .= $this->renderMonth();
                break;
        }
        
        $output .= '</table>';

        // Restore previous locale
        setlocale(LC_TIME, $oldLocale);

        return $output;
    }

    protected function renderDay()
    {
        $output = '';

        return $output;
    }

    protected function renderWeek()
    {
        $output = '';

        return $output;
    }

    protected function renderMonth()
    {
        $strDayName     = $this->showFullDayName ? '%A' : '%a';

        $output = '';

        if ($this->showMonth) {
            $output .= '<tr>';
            $output .= '    <td colspan="7">';
            $output .=          strftime('%B', $this->currentTimestamp);
            $output .= '    </td>';
            $output .= '</tr>';
        }
        if ($this->showDays) {
            $baseTs = strtotime('next ' . $this->weekStartOn);
            $output .= '<tr>';
            for ($i = 0; $i < 7; $i++) {
                $output .= '<td>';
                $output .=     strftime($strDayName, $baseTs + 3600 * 24 * $i);
                $output .= '</td>';
            }
            $output .= '</tr>';

        }
        $i = 0;
        foreach ($this->container as $offset => $day) {
            if ($i % 7 == 0) {
                $output .= '<tr>';
            }

            $output .= $day->render();
            
            if ($i % 7 == 6) {
                $output .= '</tr>';
            }
            $i++;
        }

        return $output;
    }



}