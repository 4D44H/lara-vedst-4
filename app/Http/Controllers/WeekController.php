<?php

namespace Lara\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Lara\Survey;
use Request;
use View;
use Cache;
use DateTime;
use DateInterval;
use Lara\Http\Requests;
use Lara\Http\Controllers\Controller;
use Lara\ClubEvent;
use Lara\Schedule;
use Lara\Person;
use Lara\Club;
use Lara\Section;

class WeekController extends Controller {
    
    /**
     * Fills missing parameters: if no week number specified use current year and week.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function currentWeek()
    {   $currentDate  = new DateTime();
        
	        return redirect()->action([WeekController::class, 'showWeek'], array('year' => $currentDate->format("Y"),
    	                                                             'week' => $currentDate->format('W')));
    }
	/**
	* Generate the view of the week for given month and given year
	* with events in this period.
	*
	* @param string $year
	* @param string $week 
	* @return view weekView
	*
	*
	*/
	public function showWeek($year,$week)
    {
        //override filter from request parameter , e.g filter="mi-di"
        $extraFilter = request()->has("filter")? request()->filter : '';
        
		// Create week start date on monday (day 1)
        $weekStart = date('Y-m-d', strtotime($year."W".$week.'1'));
        $weekStartDate = new DateTime($weekStart);
        $weekStartDateNext = (new DateTime($weekStartDate->format('Y-m-d')))->modify('+1 week')->modify('+3 days');
        $weekStartDatePrev = (new DateTime($weekStartDate->format('Y-m-d')))->modify('-1 week')->modify('+3 days');
        
        // Create the number of the next week
		$nextWeek = $weekStartDateNext->format("W");
		$nextYear = $weekStartDateNext->format('Y');

        // Create week end date - we go till tuesday (day 2) because café needs alternative week view (Mi-Di)
        $weekEnd = date('Y-m-d', strtotime($nextYear."W".$nextWeek.'2'));
		
 	    // Create the number of the previous week
	    $previousWeek = $weekStartDatePrev->format("W");
	    $previousYear = $weekStartDatePrev->format("Y");

		// Convert number of prev/next week to verbatim format - needed for correct << and >> button links
 	    $nextWeek 	  = $nextYear."/KW".$nextWeek;
 		$previousWeek = $previousYear."/KW".$previousWeek; 


		$date = array('year' 			=> $year, 
					  'week' 			=> $week,
					  'weekStart' 		=> $weekStart,
					  'weekEnd'			=> $weekEnd,
					  'nextWeek'		=> $nextWeek,
					  'previousWeek'	=> $previousWeek );
				       
        $events = ClubEvent::where('evnt_date_start','>=',$weekStart)
                           ->where('evnt_date_start','<=',$weekEnd)
                           ->with('section',
                           		  'showToSection',
                           		  'getSchedule.shifts.type',
                           		  'getSchedule.shifts.getPerson.getClub',
                                  'getSchedule.shifts.getPerson.user',
                                  'getSchedule.shifts.getPerson.user.section')
                           ->orderBy('evnt_date_start')
                           ->orderBy('evnt_time_start')
                           ->get();

		$surveys = Survey::where('deadline', '>=', $weekStart)
							->where('deadline', '<=', $weekEnd)
							->orderBy('deadline')
							->get();
		
		$sections = Section::where('id', '>', 0)
                         ->orderBy('title')
                         ->get(['id', 'title', 'color']);

        // Filter - Workaround for older events: populate filter with event club
        /* @var $clubEvent ClubEvent */
        foreach ($events as $clubEvent) {
	        if ($clubEvent->showToSection->isEmpty()) {
	            $clubEvent->showToSection()->sync([$clubEvent->section->id]);
	            $clubEvent->save();
	        } 
        }

		$clubs = Club::orderBy('clb_title')->pluck('clb_title', 'id');
        
        return View::make('weekView', compact('events',   'date',
        									  'weekStart', 'weekEnd', 
											  'clubs', 'surveys', 'sections', 'extraFilter'));
	}
}
