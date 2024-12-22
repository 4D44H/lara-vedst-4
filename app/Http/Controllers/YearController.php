<?php

namespace Lara\Http\Controllers;

use Lara\Section;
use Request;
use View;
use Lara\Http\Requests;
use Lara\Http\Controllers\Controller;
use Lara\ClubEvent;

class YearController extends Controller
{

    /** 
     * Fills missing parameters - if no year specified use current year.
     * 
     * @return void
     */
    public function currentYear()
    {
        return redirect()->action(
            [YearController::class, 'showYear'],
            ['year' => date("Y")]
        );
    }


    /**
     * Generates the view for the list of all events in a year.
     *
     * @param  int $year
     *
     * @return view calendarView
     * @return ClubEvent[] $events
     * @return string $date
     */
    public function showYear($year)
    {
        $yearStart = $year . '01' . '01';
        $yearEnd = $year . '12' . '31';

        $previous = $year - 1;
        $next = $year + 1;

        $date = date("Y", strtotime($yearStart));

        $events = ClubEvent::where('evnt_date_start', '>=', $yearStart)
            ->where('evnt_date_start', '<=', $yearEnd)
            ->with('section')
            ->orderBy('evnt_date_start')
            ->orderBy('evnt_time_start')
            ->get();

        $sections = Section::query()->where('id', '>', 0)
            ->orderBy('title')
            ->get(['id', 'title', 'color']);

        return View::make('listView', compact('sections', 'events', 'date', 'previous', 'next'));
    }
}
