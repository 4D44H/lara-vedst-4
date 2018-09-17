<?php

namespace Lara\Http\Controllers;

use Carbon\Carbon;
use Request;
use Redirect;
use View;

use Lara\Http\Requests;
use Lara\Http\Controllers\Controller;

use Lara\ClubEvent;
use Lara\Section;

class DateController extends Controller {

    /**
     * Fills missing parameters: if no day specified use current date.
     *
     * @return  int $day
     * @return  int $month
     * @return  int $year
     * @return RedirectResponse
     */
    public function currentDate()
    {
        return Redirect::action( 'DateController@showDate', ['year' => date("Y"),
                                                             'month' => date("m"),
                                                             'day' => date("d")] );
    }


     /**
     * Generates the view for the list of all events on a specific date.
     *
     * @param  int $year
     * @param  int $month
     * @param  int $day
     *
     * @return view calendarView
     * @return ClubEvent[] $events
     * @return string $date
     */
    public function showDate($year, $month, $day)
    {
        $dateInput = $year.$month.$day;

        $carbonDate = Carbon::createFromTimestamp(strtotime($dateInput));

        $previous = $carbonDate->subDays(1)->format('Y/m/d');
        $next = $carbonDate->addDays(2)->format('Y/m/d');

        $date = strftime("%a, %d. %b %Y", strtotime($dateInput));

        $events = ClubEvent::where('evnt_date_start','=',$dateInput)
                           ->with('section', "showToSection")
                           ->orderBy('evnt_time_start','asc')
                           ->paginate(15);

        $sections = Section::where('id', '>', 0)
                           ->orderBy('title')
                           ->get(['id', 'title', 'color']);

        return View::make('listView', compact('sections', 'events', 'date', 'previous', 'next'));
    }

}
