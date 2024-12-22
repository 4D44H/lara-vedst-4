<?php

namespace Lara\Http\Controllers;

use Auth;
use Cache;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Input;
use Lara\Club;
use Lara\ClubEvent;
use Lara\Console\Commands\SyncBDclub;
use Lara\Http\Middleware\RejectGuests;
use Lara\Role;
use Lara\ShiftType;
use Lara\Shift;
use Lara\Person;
use Lara\Schedule;
use Lara\Section;
use Lara\Template;
use Lara\Utilities;
use Lara\utilities\RoleUtility;

use View;

class ClubEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('rejectGuests', ['only' => ['create', 'edit']]);
        $this->authorizeResource(ClubEvent::class, 'event');
    }

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
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $templateId
     * @return \Illuminate\Contracts\View\View
     *
     * @return view createClubEventView
     * @return Section[] sections
     * @return Schedule[] templates
     * @return shiftTypes[] shiftTypes
     * @return string $date
     * @return \Illuminate\Http\Response
     */
    public function create($year = null, $month = null, $day = null, $templateId = null)
    {
        // Filling missing date and template number in case none are provided
        if (is_null($year)) {
            $year = date("Y");
        }

        if (is_null($month)) {
            $month = date("m");
        }

        if (is_null($day)) {
            $day = date("d");
        }

        if (is_null($templateId)) {
            $templateId = 0; // 0 = no template
        }


        // validate date, see
        // https://stackoverflow.com/questions/19271381/correctly-determine-if-date-string-is-a-valid-date-in-that-format
        $dateString = $year . "-" . $month . "-" . $day;
        $d = DateTime::createFromFormat('Y-m-d', $dateString);
        $isDateFormatValid = $d && $d->format('Y-m-d') === $dateString;

        if (!$isDateFormatValid) {
            session()->put('message', trans("messages.invalidDate", compact('day', 'month', 'year')));
            session()->put('msgType', 'danger');
            return redirect('/');
        }

        // prepare correct date format to be used in the forms
        $date = strftime("%d-%m-%Y", strtotime($year . $month . $day));

        // get a list of possible clubs to create an event at, but without the id=0 (default value)
        $sections = Section::where("id", '>', 0)
            ->orderBy('title', 'ASC')
            ->get();

        // get a list of available templates to choose from
        $allowedSections = Auth::user()->getSectionsIdForRoles(RoleUtility::PRIVILEGE_MEMBER)->toArray();
        if (Utilities::requirePermission(RoleUtility::PRIVILEGE_ADMINISTRATOR)) {
            $templates = Template::all()->sortBy('title');
        } else {
            $templates = Template::whereHas('section', function ($query) use ($allowedSections) {
                $query->whereIn('id', $allowedSections);
            })->get()->sortBy('title');
        }
        // get a list of available job types
        $shiftTypes = ShiftType::where('is_archived', '=', '0')
            ->orderBy('title', 'ASC')
            ->get();

        // if a template id was provided, load the schedule needed and extract job types
        if ($templateId != 0) {
            /** @var Template $template */
            $template = Template::where('id', '=', $templateId)
                ->first();

            // put name of the active template for further use
            $activeTemplate = $template->title;

            // get template data
            $shifts = $template->shifts()
                ->with('type')
                ->orderByRaw('position IS NULL, position ASC, id ASC')
                ->get()
                ->map(function (Shift $shift) {
                    // copy all except person_id and schedule_id and comment
                    return $shift->replicate(['person_id', 'schedule_id', 'comment']);
                });
            $title = $template->title;
            $subtitle = $template->subtitle;
            $type = $template->type;
            $section = $template->section;
            $filter = $template->showToSectionNames();
            $dv = $template->time_preparation_start;
            $timeStart = $template->time_start;
            $timeEnd = $template->time_end;
            $info = $template->public_info;
            $details = $template->private_details;
            $private = $template->is_private;
            $facebookNeeded = $template->facebook_needed == false ? null : 0;
            $priceNormal = $template->price_normal;
            $priceTicketsNormal = $template->price_tickets_normal;
            $priceExternal = $template->price_external;
            $priceTicketsExternal = $template->price_tickets_external;
            $canceled = 0;
        } else {
            // fill variables with no data if no template was chosen
            $activeTemplate = "";
            $shifts = collect([]);
            $title = null;
            $type = null;
            $subtitle = null;
            $section = Section::current();
            $filter = [Section::current()->title];
            $dv = $section->preparationTime;
            $timeStart = $section->startTime;
            $timeEnd = $section->endTime;
            $info = null;
            $details = null;
            $private = null;
            $facebookNeeded = null;
            $priceNormal = null;
            $priceTicketsNormal = null;
            $priceExternal = null;
            $priceTicketsExternal = null;
        }
        $canceled = 0;
        $createClubEvent = true;
        $eventUrl = '';

        return View::make(
            'clubevent.createClubEventView',
            compact(
                'sections',
                'shiftTypes',
                'templates',
                'shifts',
                'title',
                'subtitle',
                'type',
                'section',
                'filter',
                'timeStart',
                'timeEnd',
                'info',
                'details',
                'private',
                'dv',
                'activeTemplate',
                'date',
                'templateId',
                'facebookNeeded',
                'createClubEvent',
                'priceExternal',
                'priceNormal',
                'priceTicketsExternal',
                'priceTicketsNormal',
                'eventUrl',
                'canceled'
            )
        );
    }


    /**
     * Store a newly created resource in storage.
     * Create a new event with schedule and write it to the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        //validate passwords
        if ($request->input('password') != $request->input('passwordDouble')) {
            session()->put('message', config('messages_de.password-mismatch'));
            session()->put('msgType', 'danger');
            return back()->withInput();
        }

        $newEvent = $this->editClubEvent(null);
        $newEvent->save();

        $newSchedule = (new ScheduleController)->update(null);
        $newSchedule->evnt_id = $newEvent->id;


        $newSchedule->save();

        ScheduleController::createShifts($newSchedule);

        Utilities::clearIcalCache();
        if ($request->input('saveAsTemplate')) {
            $template = $newSchedule->toTemplate();
            $newEvent->template_id = $template->id;
            $newEvent->save();
        }

        // show new event
        return redirect()->action([ClubEventController::class, 'show'], [$newEvent->id]);
    }

    /**
     * Generates the view for a specific event, including the schedule.
     *
     * @param int $id
     * @return view ClubEventView
     * @return ClubEvent $clubEvent
     * @return Shifts[] $shifts
     * @return RedirectResponse
     */
    public function show(ClubEvent $event)
    {
        /* @var $clubEvent ClubEvent */
        $clubEvent = ClubEvent::with('section')
            ->findOrFail($event->id);

        $user = Auth::user();
        if (!$user && $clubEvent->evnt_is_private == 1) {
            session()->put('message', config('messages_de.access-denied'));
            session()->put('msgType', 'danger');
            return redirect()->action(
                [MonthController::class, 'showMonth'],
                [
                    'year' => date('Y'),
                    'month' => date('m')
                ]
            );
        }

        //ignore html tags in the info boxes
        $clubEvent->evnt_public_info = htmlspecialchars($clubEvent->evnt_public_info, ENT_NOQUOTES);
        $clubEvent->evnt_private_details = htmlspecialchars($clubEvent->evnt_private_details, ENT_NOQUOTES);

        //if URL is in one of the info boxes, convert it to clickable hyperlink (<a> tag)
        $clubEvent->evnt_public_info = Utilities::surroundLinksWithTags($clubEvent->evnt_public_info);
        $clubEvent->evnt_private_details = Utilities::surroundLinksWithTags($clubEvent->evnt_private_details);

        $schedule = Schedule::findOrFail($clubEvent->getSchedule->id);

        $shifts = Shift::where('schedule_id', '=', $schedule->id)
            ->with(
                'type',
                'getPerson',
                'getPerson.getClub'
            )
            ->orderByRaw('position IS NULL, position ASC, id ASC')
            ->get();

        $clubs = Club::orderBy('clb_title')->pluck('clb_title', 'id');

        $persons = Cache::remember('personsForDropDown', 10, function () {
            $timeSpan = new DateTime("now");
            $timeSpan = $timeSpan->sub(DateInterval::createFromDateString('3 months'));
            return Person::whereRaw("prsn_ldap_id IS NOT NULL AND (prsn_status IN ('aktiv', 'kandidat') OR updated_at>='" . $timeSpan->format('Y-m-d H:i:s') . "')")
                ->orderBy('clb_id')
                ->orderBy('prsn_name')
                ->get();
        });

        $revisions = json_decode($clubEvent->getSchedule->entry_revisions, true);

        if (!is_null($revisions)) {
            // reverse order to show latest revision first
            $revisions = array_reverse($revisions);

            // deleting ip adresses from output for privacy reasons
            foreach ($revisions as $entry) {
                unset($entry["from ip"]);
            }
            // add LDAP-ID of the event creator - only this user + Marketing + CL will be able to edit
            $created_by = $revisions[0]["user id"];
            $creator_name = $revisions[0]["user name"];
        } else {
            // workaround for empty revision in development
            $revisions = [];
            $created_by = "";
            $creator_name = "";
        }

        // Filter - Workaround for older events: populate filter with event club
        if ($clubEvent->showToSection->isEmpty()) {
            $clubEvent->showToSection()->sync([$clubEvent->section->id]);
            $clubEvent->save();
        }

        return View::make('clubevent.clubEventView', compact('clubEvent', 'shifts', 'clubs', 'persons', 'revisions', 'created_by', 'creator_name'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ClubEvent $event)
    {
        // find schedule
        $schedule = $event->getSchedule;

        // get a list of possible clubs to create an event at
        $sections = Section::where("id", '>', 0)
            ->orderBy('title', 'ASC')
            ->get();


        // get a list of available job types
        $shiftTypes = ShiftType::where('is_archived', '=', '0')
            ->orderBy('title', 'ASC')
            ->get();

        // put template data into shifts
        $shifts = $schedule->shifts()
            ->with('type')
            ->orderByRaw('position IS NULL, position ASC, id ASC')
            ->get();

        // Filter - Workaround for older events: populate filter with event club
        if ($event->showToSection->isEmpty()) {
            $event->showToSection()->sync([$event->section->id]);
            $event->save();
        }

        // add LDAP-ID of the event creator - only this user + Marketing + CL will be able to edit
        $revisions = json_decode($event->getSchedule->entry_revisions, true);
        if (is_null($revisions)) {
            $created_by = "";
            $creator_name = "";
        } else {
            $created_by = $revisions[0]["user id"];
            $creator_name = $revisions[0]["user name"];
        }

        $title = $event->evnt_title;
        $type = $event->evnt_type;
        $subtitle = $event->evnt_subtitle;
        $section = $event->section;
        $filter = $event->showToSectionNames();
        $dv = $schedule->schdl_time_preparation_start;
        $timeStart = $event->evnt_time_start;
        $timeEnd = $event->evnt_time_end;
        $info = $event->evnt_public_info;
        $details = $event->evnt_private_details;
        $private = $event->evnt_is_private;
        $date = $event->evnt_date_start;
        $priceNormal = $event->price_normal;
        $priceTicketsNormal = $event->price_tickets_normal;
        $priceExternal = $event->price_external;
        $priceTicketsExternal = $event->price_tickets_external;
        $eventUrl = $event->event_url;
        $facebookNeeded = $event->facebook_done;
        $canceled = $event->canceled == 1;

        if (!is_null($event->template_id)) {
            $baseTemplate = $event->template;
        } else {
            $baseTemplate = null;
        }

        $createClubEvent = false;

        return View::make(
            'clubevent.createClubEventView',
            compact(
                'sections',
                'shiftTypes',
                'shifts',
                'title',
                'subtitle',
                'type',
                'section',
                'filter',
                'timeStart',
                'timeEnd',
                'info',
                'details',
                'private',
                'dv',
                'date',
                'facebookNeeded',
                'createClubEvent',
                'event',
                'baseTemplate',
                'priceExternal',
                'priceNormal',
                'priceTicketsExternal',
                'priceTicketsNormal',
                'eventUrl',
                'canceled'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClubEvent $event)
    {
        //validate passwords
        if ($request->input('password') != $request->input('passwordDouble')) {
            session()->put('message', config('messages_de.password-mismatch'));
            session()->put('msgType', 'danger');
            return back()->withInput();
        }

        $event = $this->editClubEvent($event);
        $schedule = (new ScheduleController)->update($event->getSchedule->id);

        ScheduleController::editShifts($schedule);

        // save all data in the database
        $event->save();
        $schedule->save();

        Utilities::clearIcalCache();
        if ($request->boolean('saveAsTemplate') == true) {
            $template = $schedule->toTemplate();
            $event->template_id = $template->id;
            $event->save();
        }

        // show event
        return redirect()->action([ClubEventController::class, 'show'], [$id]);
    }

    /**
     * Delete an event specified by parameter $id with schedule and shifts
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(ClubEvent $event)
    {
        $date = new DateTime($event->evnt_date_start);


        // Check if event exists
        if (is_null($event)) {
            session()->put('message', config('messages_de.event-doesnt-exist'));
            session()->put('msgType', 'danger');
            return back();
        }

        // Check credentials: you can only delete, if you have rights for marketing or management.
        $revisions = json_decode($event->getSchedule->entry_revisions, true);
        $created_by = $revisions[0]["user id"];

        $user = Auth::user();
        if (!$user || !$user->isAn(RoleUtility::PRIVILEGE_MARKETING, RoleUtility::PRIVILEGE_CL, RoleUtility::PRIVILEGE_ADMINISTRATOR)) {
            session()->put('message', 'Du darfst diese Veranstaltung/Aufgabe nicht einfach löschen! Frage die Clubleitung oder Markleting ;)');
            session()->put('msgType', 'danger');
            return redirect()->action(
                [MonthController::class, 'showMonth'],
                [
                    'year' => date('Y'),
                    'month' => date('m')
                ]
            );
        }

        Utilities::clearIcalCache();

        // Delete schedule with shifts
        $result = (new ScheduleController)->destroy($event->getSchedule()->first()->id);

        // Now delete the event itself
        $event->delete();

        // show current month afterwards
        session()->put('message', config('messages_de.event-delete-ok'));
        session()->put('msgType', 'success');
        return redirect()->action([MonthController::class, 'showMonth'], [
            'year' => $date->format('Y'),
            'month' => $date->format('m')
        ]);
    }





    //--------- PRIVATE FUNCTIONS ------------


    /**
     * Edit or create a clubevent with its entered information.
     * If $id is null create a new clubEvent, otherwise the clubEvent specified by $id will be edit.
     *
     * @param ClubEvent|null $event
     * @return ClubEvent clubEvent
     */
    private function editClubEvent(ClubEvent $event = null)
    {
        if (is_null($event)) {
            $event = new ClubEvent();
            $event->creator_id = Auth::user()->id;
        }

        // format: strings; no validation needed
        $event->evnt_title = request('title');
        $event->evnt_subtitle = request('subtitle');
        $event->evnt_public_info = request('publicInfo');
        $event->evnt_private_details = request('privateDetails');
        $event->evnt_type = request('type');
        $event->facebook_done = $this->getFacebookDoneValue();
        $event->event_url = request('eventUrl', "");
        $event->price_tickets_normal = $this->getOrNullNumber('priceTicketsNormal');
        $event->price_tickets_external = $this->getOrNullNumber('priceTicketsExternal');
        $event->price_normal = $this->getOrNullNumber('priceNormal');
        $event->price_external = $this->getOrNullNumber('priceExternal');
        $event->template_id = $this->getOrNullNumber('templateId');

        // Check if event URL is properly formatted: if the protocol is missing, we have to add it.
        if ($event->event_url !== "" && parse_url($event->event_url, PHP_URL_SCHEME) === null) {
            $event->event_url = 'https://' . $event->event_url;
        }

        // create new section
        if (!Section::where('id', '=', request('section'))->first()) {
            $section = new Section;
            $section->title = request('section');
            $section->save();

            $event->plc_id = $section->id;
        } // use existing section
        else {
            $event->plc_id = Section::where('id', '=', request('section'))->first()->id;
        }

        // format: date; validate on filled value
        if (!empty(request('beginDate'))) {
            $newBeginDate = new DateTime(request('beginDate'), new DateTimeZone(config('app.timezone')));
            $event->evnt_date_start = $newBeginDate->format('Y-m-d');
        } else {
            $event->evnt_date_start = date('Y-m-d', mktime(0, 0, 0, 0, 0, 0));;
        }

        if (!empty(request('endDate'))) {
            $newEndDate = new DateTime(request('endDate'), new DateTimeZone(config('app.timezone')));
            $event->evnt_date_end = $newEndDate->format('Y-m-d');
        } else {
            $event->evnt_date_end = date('Y-m-d', mktime(0, 0, 0, 0, 0, 0));;
        }
        if (!empty(request('unlockDate'))) {
            $event->unlock_date = Carbon::createFromFormat('Y-m-d\TH:i', request('unlockDate'));
        } else {
            $event->unlock_date = null;
        }

        // format: time; validate on filled value
        $event->evnt_time_start = !empty(request('beginTime')) ? request('beginTime') : mktime(0, 0, 0);
        $event->evnt_time_end = !empty(request('endTime')) ? request('endTime') : mktime(0, 0, 0);

        // format: tinyInt; validate on filled value
        // reversed this: input=1 means "event is public", input=0 means "event is private"
        $event->evnt_is_private = (request('isPrivate') == '1') ? 0 : 1;
        $event->canceled = (request('isCanceled') == '1');
        //dd((request('isCanceled') == '1'));
        $eventIsPublished = request('evntIsPublished');

        if (!is_null($eventIsPublished)) {
            //event is pubished
            $event->evnt_is_published = (int) $eventIsPublished;
        } elseif (Auth::user()->hasPermissionsInSection($section, RoleUtility::PRIVILEGE_MARKETING)) {
            // event was unpublished
            $event->evnt_is_published = 0;
        }


        // Filter
        $filter = collect(request("filter"))->values()->toArray();

        $event->save();
        $event->showToSection()->sync($filter);
        return $event;
    }

    private function getOrNullNumber($query)
    {
        $num = request($query);
        if (doubleval($num) > 0) {
            return $num;
        } else {
            return null;
        }
    }

    private function getFacebookDoneValue()
    {
        $inputVal = request('facebookDone');
        switch ($inputVal) {
            case "1":
                return true;
            case "0":
                return false;
            case "-1":
            default:
                return null;
        }
    }
}
