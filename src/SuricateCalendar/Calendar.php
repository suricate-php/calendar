<?php
namespace SuricateCalendar;

/**
 * @property string locale locale used by calendar
 * @property int displayMode
 * @property boolean showDays
 * @property boolean showMonth
 * @property boolean showItems
 * @property boolean showItemsNb
 * @property boolean showFullDayName
 * @property string weekStartOn
 * @property string externalStylesheet
 * @property string internalStylesheet
 * @property string styleBorderColor
 * @property string styleMonthBgColor
 * @property string styleMonthColor
 * @property string styleMonthAlign
 * @property string styleDaysBgColor
 * @property string styleDaysColor
 * @property string styleDaysAlign
 * @property string styleCellBgColor
 * @property string styleCellAlign
 * @property string styleCellColor
 * @property string styleCellWidth
 * @property string styleCellHeight
 * @property string styleCellActiveBgColor
 * @property string styleCellActiveAlign
 * @property string styleCellActiveColor
 * @property string styleTodayBgColor
 * @property string styleTodayAlign
 * @property string styleTodayColor
 * @property string stylePastBgColor
 * @property string stylePastAlign
 * @property string stylePastColor
 * @property string styleFutureBgColor
 * @property string styleFutureAlign
 * @property string styleFutureColor
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
            'showItems'     => false,
            'showItemsNb'   => false,

            // week params

            // day params

            // merged params
            'showFullDayName'       => true,
            'weekStartOn'           => 'sunday',

            // styling
            'externalStylesheet'    => null,
            'internalStylesheet'    => true,
            'styleBorderColor'      => '#000',
            
            'styleMonthBgColor'     => '#C0C0C0',
            'styleMonthColor'       => '#FFF',
            'styleMonthAlign'       => 'center',

            'styleDaysBgColor'     => '#e7e7e7',
            'styleDaysColor'       => '#777',
            'styleDaysAlign'       => 'center',

            'styleCellBgColor'      => '#FFF',
            'styleCellAlign'        => 'center',
            'styleCellColor'        => '#777',
            'styleCellWidth'        => 'auto',
            'styleCellHeight'       => 'auto',

            'styleCellActiveBgColor'=> '#3a87ad',
            'styleCellActiveAlign'  => 'center',
            'styleCellActiveColor'  => '#FFF',

            'styleTodayBgColor'     => '#E0E0E0',
            'styleTodayAlign'       => 'center',
            'styleTodayColor'       => '#000',

            'stylePastBgColor'     => '#FFF',
            'stylePastAlign'       => 'center',
            'stylePastColor'       => '#CCC',

            'styleFutureBgColor'     => '#FFF',
            'styleFutureAlign'       => 'center',
            'styleFutureColor'       => '#CCC',

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
        $firstDayOfMonth = mktime(0, 0, 0, $this->month, 1, $this->year);
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

            if ($this->showItems) {
                $item->showItems = true;
            } elseif ($this->showItemsNb) {
                $item->showItemsNb = true;
            }

            $this->container[$dayIdentifier] = $item;
        }
    }

    public function setContent($items)
    {
        foreach ($items as $dayIdentifier => $item) {
            if (isset($this->container[$dayIdentifier])) {
                $this->container[$dayIdentifier]->items = $item;
            }
        }

        return $this;
    }

    public function render()
    {
        $output = '';

        $oldLocale = setlocale(LC_TIME, 0);
        
        if (!setlocale(LC_TIME, $this->locale)) {
            throw new \InvalidArgumentException('Missing locale on system : ' . $this->locale);
        }
        $output  = $this->renderStylesheet();
        $output .= '<table border="1" class="calendar" id="calendar-' . $this->calendarId . '">';

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
            $output .= '    <td colspan="7" class="month">';
            $output .=          strftime('%B', $this->currentTimestamp);
            $output .= '    </td>';
            $output .= '</tr>';
        }
        if ($this->showDays) {
            $baseTs = strtotime('next ' . $this->weekStartOn);
            $output .= '<tr>';
            for ($i = 0; $i < 7; $i++) {
                $output .= '<td class="days">';
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

    private function renderStylesheet()
    {
        if ($this->externalStylesheet !== null) {
            $output = '<link rel="stylesheet" type="text/css" href="' . $this->externalStylesheet . '" />';
        } elseif ($this->internalStylesheet) {
            $calendarIdentifier = '#calendar-' . $this->calendarId;

            $output = '<style type="text/css">';
            $output .= <<<EOD
            $calendarIdentifier {
                border: solid 1px {$this->styleBorderColor};
            }
            $calendarIdentifier td.month {
                text-align: {$this->styleMonthAlign};
                color: {$this->styleMonthColor};
                background-color: {$this->styleMonthBgColor};
            }

            $calendarIdentifier td.days {
                text-align: {$this->styleDaysAlign};
                color: {$this->styleDaysColor};
                background-color: {$this->styleDaysBgColor};
            }

            $calendarIdentifier td.day {
                background-color: {$this->styleCellBgColor};
                text-align: {$this->styleCellAlign};
                width: {$this->styleCellWidth};
                height: {$this->styleCellHeight};
            }

            $calendarIdentifier td.day.past {
                background-color: {$this->stylePastBgColor};
                color: {$this->stylePastColor};
                text-align: {$this->stylePastAlign};
            }

            $calendarIdentifier td.day.today {
                background-color: {$this->styleTodayBgColor};
                color: {$this->styleTodayColor};
                text-align: {$this->styleTodayAlign};
            }

            $calendarIdentifier td.day.has-items {
                background-color: {$this->styleCellActiveBgColor};
                color: {$this->styleCellActiveColor};
                text-align: {$this->styleCellActiveAlign};
            }

            $calendarIdentifier td.day.future {
                background-color: {$this->styleFutureBgColor};
                color: {$this->styleFutureColor};
                text-align: {$this->styleFutureAlign};
            }
EOD;
            $output .= '</style>';

            return $output;
        }
    }
}
