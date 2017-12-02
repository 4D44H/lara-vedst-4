<?php

namespace Lara\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Lara\Club;
use Lara\Section;
use Lara\Utilities;
use Lara\ClubEvent;
use Session;
use View;
use Redirect;
use Log;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = Section::orderBy('title', 'ASC')->paginate(25);

        return view('manageSections', ['sections' => $sections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $current_section = new Section();
        $current_section->title= trans('mainLang.newSection');
        return View::make('sectionView', compact('current_section'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Utilities::requirePermission("admin")) {
            // Return to the section management page
            Session::put('message', trans('mainLang.adminsOnly'));
            Session::put('msgType', 'danger');

            return Redirect::back();
        }

        $rules = array(
            'title' => 'required',
            'color' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        $title = Input::get("title");
        $id = Input::get("id");
        $color = Input::get("color");
        $isNew = strlen($id) == 0;


        if ($validator->fails()) {
           return Redirect::back()->withErrors($validator);
        }

        if ($isNew) {
            $existingSection = Section::where('title', '=', $title)->first();
            if (!is_null($existingSection)) {
                // Return to the section management page
                Session::put('message', trans('mainLang.sectionExists'));
                Session::put('msgType', 'danger');
                return Redirect::back();
            }
            $section = new Section();
            $section->section_uid = hash("sha512", uniqid());
            $club = new Club();
        } else {
            $section = Section::where('id', '=', $id)->first();
            $existingSection = Section::where('title', '=', $title)->where('id', '!=', $id)->first();
            if (!is_null($existingSection)) {
                // Return to the section management page
                Session::put('message', trans('mainLang.sectionExists'));
                Session::put('msgType', 'danger');
                return Redirect::back();
            }
            $club = Club::where('clb_title','=',$section->title)->first();
            if(is_null($club)){
                $club = new Club();
            }
        }

        $section->title = $title;
        $section->color = $color;
        $section->save();
        $club->clb_title = $title;
        $club->save();

        // Return to the the section management page
        Session::put('message', trans('mainLang.changesSaved'));
        Session::put('msgType', 'success');
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get selected shiftTypes
        $current_section = Section::findOrFail($id);

        return View::make('sectionView', compact('current_section'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Lara\Section $section
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Lara\Section $section
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section $section)
    {
        return self::store($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Lara\Section $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(Section $section)
    {
        if (!Utilities::requirePermission("admin")) {
            // Return to the section management page
            Session::put('message', trans('mainLang.adminsOnly'));
            Session::put('msgType', 'danger');
        }

        // Log the action while we still have the data
        Log::info('Section removed: ' .
            Session::get('userName') . ' (' . Session::get('userId') . ', ' . Session::get('userGroup') .
            ') deleted section "' . $section->title .  '". May Gods have mercy on their souls!');

        $events = ClubEvent::where("plc_id", "=", $section->id)->get();
        /* @var $event ClubEvent */
        foreach ($events as $event) {
             // Delete schedule with shifts
            (new ScheduleController)->destroy($event->getSchedule()->first()->id);

            // Now delete the event itself
            ClubEvent::destroy($event->id);
        }
        
        //find according clubs
        $clubs=Club::where('clb_title','=',$section->title)->get();
        foreach ($clubs as $club){
            Club::destroy($club->id);
        }

        // Now delete the section
        Section::destroy($section->id);

        // Return to the management page
        Session::put('message', trans('mainLang.changesSaved'));
        Session::put('msgType', 'success');
        return Redirect::action( 'SectionController@index' );
        
    }
}
