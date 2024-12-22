<?php

namespace Lara\Http\Controllers;

use Carbon\Carbon;
use Request;
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
        return redirect()->action( [DateController::class,'showDate'], 
        ['year' => date("Y"),
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
        $inputDate = Carbon::create($year,$month,$day);
        $carbonDate = clone $inputDate;

        $previous = $carbonDate->subDays(1)->format('Y/m/d');
        $next = $carbonDate->addDays(2)->format('Y/m/d');

        $date = strftime("%a, %d. %b %Y", strtotime($inputDate->format("Ymd")));
        
        $events = ClubEvent::query()
                           ->whereRaw("? BETWEEN evnt_date_start AND DATE_ADD(concat(evnt_date_end , ' ' , evnt_time_end), INTERVAL -5 HOUR)",[$inputDate->format('Y-m-d')])
                           ->with('section', "showToSection")
                           ->orderBy('evnt_time_start','asc')->get();
                           
        
        $sections = Section::where('id', '>', 0)
                           ->orderBy('title')
                           ->get(['id', 'title', 'color']);

        return View::make('listView', compact('sections', 'events', 'date', 'previous', 'next'));
    }

}
