<?php

namespace Lara\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Lara\Club;
use Lara\Http\Middleware\AdminOnly;
use Lara\Http\Middleware\ClOnly;
use Lara\Role;
use Lara\Section;
use Lara\Template;
use Lara\User;
use Lara\Utilities;
use Lara\ClubEvent;
use Lara\utilities\RoleUtility;

use View;
use Log;

class SectionController extends Controller
{

    public function __construct()
    {
        $this->middleware(ClOnly::class);
        $this->middleware(AdminOnly::class, ['only' => ['create', 'destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** if you are an admin, you can see all */
        if (Auth::user()->isAn(RoleUtility::PRIVILEGE_ADMINISTRATOR)) {
            $sections = Section::query()->orderBy('title', 'ASC')->get();
        } else {
            /** if you are just an CL you can only edit the section  where you have the permissions */
            $sections = Auth::user()->roles()->where('name', '=', RoleUtility::PRIVILEGE_CL)->get()->map(function (Role $role) {
                return $role->section;
            })->unique();
        }

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
        $current_section->title = trans('mainLang.newSection');
        return View::make('sectionView', compact('current_section'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = array(
            'title' => 'required',
            'color' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);

        $title =            $request->input("title");
        $id =               $request->input("id");
        $color =            $request->input("color");
        $preparationTime =  $request->input("preparationTime");
        $startTime =        $request->input("startTime");
        $endTime =          $request->input("endTime");
        $isNew =            strlen($id) == 0;
        $isNamePrivate =    $request->input("is_name_private") == 'true';


        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        if ($isNew) {
            $existingSection = (new Section)->where('title', '=', $title)->first();
            if (!is_null($existingSection)) {
                // Return to the section management page
                session()->put('message', trans('mainLang.sectionExists'));
                session()->put('msgType', 'danger');
                return back();
            }
            $section = new Section();
            $section->section_uid = hash("sha512", uniqid());
            $club = new Club();
        } else {
            $section = Section::where('id', '=', $id)->first();
            $existingSection = Section::where('title', '=', $title)->where('id', '!=', $id)->first();
            if (!is_null($existingSection)) {
                // Return to the section management page
                session()->put('message', trans('mainLang.sectionExists'));
                session()->put('msgType', 'danger');
                return back();
            }
            $club = Club::where('clb_title', '=', $section->title)->first();
            if (is_null($club)) {
                $club = new Club();
            }
        }

        $section->title =           $title;
        $section->color =           $color;
        $section->preparationTime = $preparationTime;
        $section->startTime =       $startTime;
        $section->endTime =         $endTime;
        $section->is_name_private = $isNamePrivate;
        $section->save();
        if ($isNew) {
            RoleUtility::createRolesForNewSection($section);
        }

        $club->clb_title =          $title;
        $club->save();

        // Return to the the section management page
        session()->put('message', trans('mainLang.changesSaved'));
        session()->put('msgType', 'success');
        return redirect()->action([SectionController::class, 'index']);
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Section $section)
    {
        return self::store($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Lara\Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Section $section)
    {
        // Log the action while we still have the data
        /** @var User $user */
        $user = Auth::user();
        $userGroups = $user->roles()->get()->map(function (Role $role) {
            return $role->name;
        })->toArray();
        $person = $user->person;

        Log::info('Section removed: ' .
            $person->prsn_name . ' (' . $person->prsn_ldap_id . ', ' . implode(', ', $userGroups) .
            ') deleted section "' . $section->title .  '". May Gods have mercy on their souls!');

        $events = (new \Lara\ClubEvent)->where("plc_id", "=", $section->id)->get();
        /* @var $event ClubEvent */
        foreach ($events as $event) {
            // Delete schedule with shifts
            (new ScheduleController)->destroy($event->getSchedule()->first()->id);

            // Now delete the event itself
            ClubEvent::destroy($event->id);
        }

        //find according clubs
        $clubs = (new Club)->where('clb_title', '=', $section->title)->get();
        foreach ($clubs as $club) {
            Club::destroy($club->id);
        }

        //delete all roles assiged to this section
        try {
            (new Role())->where('section_id', '=', $section->id)->delete();
        } catch (\Exception $e) {
            Log::error('cannot delete roles', [$e]);
        }

        $templates = $section->templates;

        $templates->each(function (Template $template) {
            $template->delete();
        });

        // Now delete the section
        Section::destroy($section->id);

        // Return to the management page
        session()->put('message', trans('mainLang.changesSaved'));
        session()->put('msgType', 'success');
        return redirect()->action([SectionController::class, 'index']);
    }
}
