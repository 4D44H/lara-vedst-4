<?php

namespace Lara\Http\Controllers;

use DateInterval;
use DatePeriod;
use DateTime;
use Lara\ClubEvent;
use Lara\Http\Middleware\RejectGuests;
use Lara\Section;
use Lara\Survey;
use Lara\utilities\CacheUtility;
use View;


class MonthController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(RejectGuests::class, ['only' => ['markShiftsOfCurrentUser']]);
    }
    
    /**
     * Fills missing parameters: if no month specified use current year and month.
     *
     * @return int $month
     * @return int $year
     * @return RedirectResponse
     */
    public function currentMonth()
    {
        return redirect()->action([MonthController::class, 'showMonth'], [
            'year'  => date("Y"),
            'month' => date('m'),
        ]);
    }
    
    /**
     * Generate the view of the calender for given month and given year
     * with events in this period.
     *
     * @param string $year
     * @param string $month
     * @return view monthView
     */
    public function showMonth($year, $month)
    {
        list($firstDay, $lastDay, $date) = $this->makeDateArray($year, $month);
        
        $sections = Section::where('id', '>', 0)
            ->orderBy('title')
            ->get(['id', 'title', 'color']);
        
        return View::make('monthView',
            compact('date', 'firstDay', 'lastDay',  'sections'));
    }
    
    public function monthViewTable($year, $month) {
        return CacheUtility::remember('monthtable-'.$year.'-'.$month, function () use ($year, $month){
            list($firstDay, $lastDay, $date) = $this->makeDateArray($year, $month);
            $events = ClubEvent::where('evnt_date_start', '>=', $firstDay->format("Y-m-d"))
                ->where('evnt_date_start', '<=', $lastDay->format("Y-m-d"))
                ->with('showToSection')
                ->with('shifts')
                ->with('section')
                ->orderBy('evnt_date_start')
                ->orderBy('evnt_time_start')
                ->orderBy('plc_id')
                ->get();
    
            $surveys = Survey::where('deadline', '>=', $firstDay->format("Y-m-d"))
                ->where('deadline', '<=', $lastDay->format("Y-m-d"))
                ->orderBy('deadline')
                ->get();
    
            $mondays = new DatePeriod($firstDay, new DateInterval('P1W'), $lastDay->modify('+1 day'));
    
            $sections = Section::where('id', '>', 0)
                ->orderBy('title')
                ->get(['id', 'title', 'color']);
    
            return response()->json(['data'=> View::make('partials.month.monthTable',compact('events','surveys','mondays','sections','firstDay','lastDay','date'))->render()]);
        });
    }
    
    public function markShiftsOfCurrentUser($year, $month)
    {
        // create a DateTime object represeting the first monday in the timespan we want to display
        $firstDay = new DateTime($year.$month.'01');
        if ($firstDay->format('w') !== '1') {
            $firstDay->modify('previous Monday');
        }
        
        // create a DateTime object representing the last sunday in the timespan we want to display
        $lastDay = new DateTime($year.$month.$firstDay->format('t'));
        if ($lastDay->format('w' !== 0)) {
            $lastDay->modify('next Sunday');
        }
        
        $clubEventIds = ClubEvent::query()
            ->where('evnt_date_start', '>=', $firstDay)
            ->where('evnt_date_start', '<=', $lastDay)
            ->whereHas('shifts', function (\Illuminate\Database\Eloquent\Builder $query) {
                $query->where('person_id', '=', \Auth::user()->person->id);
            })->get(['id']);
        
        
        return response()->json($clubEventIds->toArray());
    }
    
    /**
     * @param $year
     * @param $month
     * @return array
     * @throws \Exception
     */
    private function makeDateArray($year, $month)
    {
        // create a DateTime object represeting the first monday in the timespan we want to display
        $firstDay = new DateTime($year.$month.'01');
        if ($firstDay->format('w') !== '1') {
            $firstDay->modify('previous Monday');
        }
        
        // create a DateTime object representing the last sunday in the timespan we want to display
        $lastDay = new DateTime($year.$month.$firstDay->format('t'));
        if ($lastDay->format('w' !== 0)) {
            $lastDay->modify('next Sunday');
        }
        
        // Create timestamp of the first day of selected month. Time 12:12:12 used only as dummy time
        $usedTime = mktime(12, 12, 12, $month, 1, $year);
        
        // Create a int with the number of days of the month (28...31)
        $daysOfMonth = date("t", $usedTime);
        
        // Create a timestamp with start of month
        $startStamp = mktime(0, 0, 0, date("n", $usedTime), 1, date("Y", $usedTime));
        
        // Int for the start day
        $startDay = date("N", $startStamp);
        
        // Int for the lst day
        $endDay = date("N", mktime(0, 0, 0, date("n", $usedTime), $daysOfMonth, date("Y", $usedTime)));
        
        $date = [
            'year'       => $year,
            'month'      => $month,
            'startDay'   => $startDay,
            'endDay'     => $endDay,
            'startStamp' => $startStamp,
            'usedTime'   => $usedTime,
            'monthName'  => date("F", $usedTime),
            'firstDay'   => $firstDay,
            'lastDay'    => $lastDay,
        ];
        
        return [$firstDay, $lastDay, $date];
    }
    
    
}
