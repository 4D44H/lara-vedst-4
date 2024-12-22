<?php

namespace Lara\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Lara\ClubEvent;
use Lara\Schedule;
use Lara\Section;
use Lara\Shift;
use Lara\ShiftType;
use Lara\Template;
use Lara\Utilities;
use Lara\utilities\RoleUtility;
use Log;

use View;


class ShiftTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function find($query = NULL)
    {
        if ( is_null($query) ) { $query = ""; } // if no parameter specified - empty means "show all"

        $shiftTypes =  ShiftType::where('title', 'like', '%' . $query . '%')
            ->orderBy('title')
            ->get([
                'title',
                'start',
                'end',
                'statistical_weight'
            ]);

        return response()->json($shiftTypes);
    }

    public function search(Request $request) {
        $filter = $request->input('filter');
        return redirect(route('shiftTypeSearch', ['filter'=>$filter]));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($filter = NULL)
    {
        $shiftTypeQuery = ShiftType::query();
        if(!is_null($filter)) {
            $shiftTypeQuery->where('title','like','%'.$filter.'%');
        }
        $shiftTypes = $shiftTypeQuery
            ->orderBy('title', 'ASC')
            ->orderBy('start')
            ->orderBy('end')
            ->paginate();
        $allShiftTypes = ShiftType::query()->orderBy('title','ASC')->orderBy('start')->orderBy('end')->get();

        return view('shifttypes.manageShiftTypesView', ['shiftTypes' => $shiftTypes,'allShiftTypes'=>$allShiftTypes]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get selected shiftTypes
        $current_shiftType = ShiftType::with('shifts')->findOrFail($id);

        // get a list of all available job types
        $shiftTypes = ShiftType::orderBy('title', 'ASC')->get();

        $shifts = Shift::where('shifttype_id', '=', $id)
            ->with('schedule.event.section')
            ->paginate(25);
        $templatesQuery = Template::whereHas('shifts', function ($query) use ($id) {
            $query->where('shifttype_id','=',$id);
        })->with('section')
        ->orderBy('title');

        $templates = $templatesQuery->get();

        return View::make('shifttypes.shiftTypeView', compact('current_shiftType', 'shiftTypes', 'shifts', 'templates'));
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check credentials: you can only edit, if you have rights for marketing, section management or admin
        $user = Auth::user();

        if(!$user || !$user->isAn(RoleUtility::PRIVILEGE_MARKETING, RoleUtility::PRIVILEGE_CL, RoleUtility::PRIVILEGE_ADMINISTRATOR))
        {
            session()->put('message', trans('mainLang.cantTouchThis'));
            session()->put('msgType', 'danger');
            return back();
        }

        // Get all the data (throws a 404 error if shiftType doesn't exist)
        $shiftType = ShiftType::findOrFail($id);

        // Extract request data
        $newTitle       = $request->input('title'.$id);
        $newTimeStart   = $request->input('start'.$id);
        $newTimeEnd     = $request->input('end'.$id);
        $newWeight      = $request->input('statistical_weight'.$id);

        // Check for empty values
        if (empty($newTitle) || empty($newTimeStart) || empty($newTimeEnd)) {
            session()->put('message', trans('mainLang.cantBeBlank'));
            session()->put('msgType', 'danger');
            return back();
        }

        // Statistical weight must be numerical
        if (!is_numeric($newWeight)) {
            session()->put('message', trans('mainLang.nonNumericStats'));
            session()->put('msgType', 'danger');
            return back();
        }

        // Statistical weight must be non-negative
        if ($newWeight < 0.0) {
            session()->put('message', trans('mainLang.negativeStats'));
            session()->put('msgType', 'danger');
            return back();
        }

        // Log the action while we still have the data
        Log::info('ShiftType edited: ' .
            $user->name . ' (' . $user->person->prsn_ldap_id . ', ' .
            ') changed shift type #' . $shiftType->id . ' from "' . $shiftType->title . '", start: ' . $shiftType->start . ', end: ' . $shiftType->end . ', weight: ' . $shiftType->statistical_weight . ' to "' . $newTitle . '" , start: ' . $newTimeStart . ', end: ' . $newTimeEnd . ', weight: ' . $newWeight . '. ');

        // Write and save changes
        $shiftType->title               = $newTitle;
        $shiftType->start          = $newTimeStart;
        $shiftType->end            = $newTimeEnd;
        $shiftType->statistical_weight  = $newWeight;
        $shiftType->save();

        // Return to the shiftType page
        session()->put('message', trans('mainLang.changesSaved'));
        session()->put('msgType', 'success');
        return back();
    }

    /**
     * Override the data of a shift with a new ShiftType
     * set the shifttype of shift to the new value
     * override start, end, and statistical_weigt
     */
    public function overrideShiftType(Request $request)
    {

        $shiftId = $request->input('shift');
        $shiftTypeNewId = $request->input('shiftType');

        $this->replaceShiftType($shiftId, $shiftTypeNewId);
        return back();

    }

    public function completeOverrideShiftType(Request $request)
    {
        $shiftTypeId = $request->input('shift');
        $shiftTypeNewId = $request->input('shiftType');

        /** @var ShiftType $shiftType */
        $shiftType = ShiftType::findOrFail($shiftTypeId);
        $shifts = $shiftType->shifts()->get();
        if (!Utilities::requirePermission('admin')) {
            $shiftFilter = $shifts->filter(function (Shift $shift) {
                /** @var Schedule $schedule */
                $schedule = $shift->schedule;
                if (is_null($schedule)) {
                    return false;
                }
                /** @var ClubEvent $event */
                $event = $schedule->event;
                if (is_null($event)) {
                    return false;
                }
                /** @var Section $section */
                $section = $event->section;
                return Auth::user()->getSectionsIdForRoles(RoleUtility::PRIVILEGE_MARKETING)->contains($section->id)  ;
            });
            $templateShift = Template::whereHas('shifts', function ($query) use ($shiftTypeId) {
                $query->where('shifttype_id', '=', $shiftTypeId);
            })->whereHas('section', function ($query) {
                $query->whereIn('id', Auth::user()->getSectionsIdForRoles(RoleUtility::PRIVILEGE_MARKETING)->toArray());
            })->get()->flatMap(function (Template $template) {
                return $template->shifts()->get();
            })->filter(function (Shift $shift) use ($shiftTypeId) {
                return $shift->shifttype_id == $shiftTypeId;
            });
            $result = collect([]);
            $shiftFilter->each(function ($shift) use ($result) {
                $result->push($shift);
            });
            $templateShift->each(function ($shift) use ($result) {
                $result->push($shift);
            });
            $shifts = $result;
        }

        $shifts->each(function ($shift) use ($shiftTypeNewId) {
            $this->replaceShiftType($shift->id, $shiftTypeNewId);
        });
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check credentials: you can only delete, if you have rights for marketing, section management or admin
        $user = Auth::user();

        if(!$user || !$user->isAn(RoleUtility::PRIVILEGE_MARKETING, RoleUtility::PRIVILEGE_CL, RoleUtility::PRIVILEGE_ADMINISTRATOR))
        {
            session()->put('message', trans('mainLang.cantTouchThis'));
            session()->put('msgType', 'danger');
            return back();
        }

        // Get all the data
        // (throws a 404 error if shiftType doesn't exist)
        $shiftType = ShiftType::findOrFail($id);

        // Before deleting, check if this job type is in use in any existing schedule
        if (  Shift::where('shifttype_id', '=', $shiftType->id)->count() > 0  ) {
            // CASE 1: job type still in use - let the user decide what to do in each case

            // Inform the user about the redirect and go to detailed info about the job type selected
            session()->put('message', trans('mainLang.deleteFailedShiftTypeInUse'));
            session()->put('msgType', 'danger');
            return redirect()->action([ShiftTypeController::class, 'show'], [ $shiftType->id] );
        }
        else
        {
            // CASE 2: job type is not used anywhere and can be remove without side effects

            // Log the action while we still have the data
            Log::info('ShiftType deleted: ' .
                $user->name . ' (' . $user->person->prsn_ldap_id . ', ' .
                ') deleted "' . $shiftType->title .  '" (it was not used in any schedule).');

            // Now delete the shiftType
            ShiftType::destroy($id);

            // Return to the management page
            session()->put('message', trans('mainLang.changesSaved'));
            session()->put('msgType', 'success');
            return redirect()->action([ShiftTypeController::class, 'show']);
        }
    }

    /**
     * @param $shiftId
     * @param $shiftTypeNewId
     */
    private function replaceShiftType($shiftId, $shiftTypeNewId)
    {
        try {
            $shift = Shift::findOrFail($shiftId);
            $shiftType = ShiftType::findOrFail($shiftTypeNewId);

            $shift->shifttype_id = $shiftType->id;
            $shift->start = $shiftType->start;
            $shift->end = $shiftType->end;
            $shift->statistical_weight = $shiftType->statistical_weight;
            $shift->save();

            session()->put('message', trans('mainLang.changesSaved'));
            session()->put('msgType', 'success');
        } catch (\Exception $e) {
            Log::error('error', [$e]);
            session()->put('message', trans('mainLang.error'));
            session()->put('msgType', 'danger');
        }
    }
}
