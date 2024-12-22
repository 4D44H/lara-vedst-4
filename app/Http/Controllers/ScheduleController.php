<?php

namespace Lara\Http\Controllers;

use DateTime;
use Request;

use Input;
use Hash;
use Lara\Logging;
use Illuminate\Database\Eloquent\Collection;

use Lara\Schedule;
use Lara\Shift;
use Lara\ShiftType;

class ScheduleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

   /**
    * Update the specified resource in storage.
    * Edit or create a schedule with its entered information.
    * If $scheduleId is null create a new schedule, otherwise the schedule specified by $scheduleId will be edited.
    *
    * Should be static to be accessed from ClubEventController
    *
    * @param int $scheduleId
    * @return Schedule newSchedule
     */
    public function update($scheduleId)
    {
        $schedule = new Schedule;
        $request= request();

        if (!is_null($scheduleId))
        {
            $schedule = Schedule::findOrFail($scheduleId);
        }

        // format: time; validate on filled value
        if(!empty($request->input('preparationTime')))
        {
            $schedule->schdl_time_preparation_start = $request->input('preparationTime');
        }
        else
        {
            $schedule->schdl_time_preparation_start = mktime(0, 0, 0);
        }

        // format: password; validate on filled value
        if ($request->input('password') == "delete" && $request->input('passwordDouble') == "delete") {
            $schedule->schdl_password = '';
        }
        elseif (!empty($request->input('password'))
            && !empty($request->input('passwordDouble'))
            && $request->input('password') == $request->input('passwordDouble')) {
            $schedule->schdl_password = Hash::make($request->input('password'));
        }

        return $schedule;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        // Get all the data
        $schedule = Schedule::find($id);

        // Check if the schedule exists
        if ( is_null($schedule) ) {
            session()->put('message', 'Fehler: Löschvorgang abgebrochen - der Dienstplaneintrag existiert nicht.');
            session()->put('msgType', 'danger');
            return back();
        }
        // Delete all corresponding shifts first because of dependencies in database
        foreach ( $schedule->shifts as $shift ) {
            ShiftController::delete($shift);
        }

        // Delete the schedule
        Schedule::destroy($id);
    }

    /**
    * Create all new shifts with entered information.
    *
    * @return Collection shifts
    */
    public static function makeShiftsFromRequest($schedule, $isNewEvent = true)
    {
        $request=request();
        $inputShifts = $request->input("shifts");
        $amount = is_null($inputShifts) ? 0 : count($inputShifts["title"]);
        $currentShiftIds = $inputShifts["id"] ? $inputShifts["id"] : [];
        $schedule->shifts()
            ->whereNotIn('id', $currentShiftIds)
            ->get()
            ->each(function(Shift $shift) {
                Logging::shiftDeleted($shift);
                $shift->delete();
            });


        for ($i = 0; $i < $amount; ++$i) {

            $title = $inputShifts["title"][$i];
            $id = $inputShifts["id"][$i];
            $type = $inputShifts["type"][$i];
            $start = $inputShifts["start"][$i];
            $end = $inputShifts["end"][$i];
            $weight = $inputShifts["weight"][$i];
            $optional = $inputShifts["optional"][$i] === "on"? 1 : 0;

            $position = $i;

            ShiftController::makeShift($schedule, $isNewEvent, $title, $id, $type, $start, $end, $weight, $position, $optional);
        }
    }


    /**
    * Edit and/or delete shifts refered to $scheduleId.
    *
    * @param Schedule $schedule
    * @return Collection shifts
    */
    public static function editShifts($schedule)
    {
        self::makeShiftsFromRequest($schedule, false);
    }

    public static function createShifts($schedule)
    {
        self::makeShiftsFromRequest($schedule, true);
    }

    /**
     * Receives a timestamp, compares it to last update time of the schedule
     * and returns either a false boolean for "no updates since timestamp provided"
     * or a JSON array of updated shifts
     *
     * @param int $scheduleId
     * @param String $timestamp
     *
     * @return \Illuminate\Http\Response
     */
    public static function getUpdates($scheduleId, $timestamp)
    {
        $updated = Schedule::where("id", "=",  $scheduleId)->first()->updated_at;
        return response()->json($updated, 200);
    }

}
