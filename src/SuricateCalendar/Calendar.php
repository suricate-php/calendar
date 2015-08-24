<?php

namespace SuricateCalendar;

class Calendar
{
    protected $displayDays  = false;
    protected $calendarId   = 1;
    protected $locale       = 'fr_FR';
    protected $date;
    
    public function __construct()
    {
        $this->date = date('Ymd');
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function setDate($date)
    {
        return $this;
    }

    public function render()
    {
        $oldLocale = setlocale(LC_TIME);
        setlocale(LC_TIME, $this->locale);


        // Restore previous locale
        setlocale(LC_TIME, $oldLocale);

    }
}