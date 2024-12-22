<?php
/**
 * User: fabian
 * Date: 31.01.17
 * Time: 00:06
 */

namespace Lara\Http\Controllers;

use Auth;
use Cache;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Entity\TimeZone;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\Alarm\DisplayAction;
use Eluceo\iCal\Domain\ValueObject\Alarm\RelativeTrigger;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Lara\ClubEvent;
use Lara\Person;
use Lara\Section;
use Lara\Shift;
use Lara\Utilities;
use Lara\utilities\RoleUtility;
use Log;

use URL;

/** Controller for generating iCal feeds */
class IcalController extends Controller
{
    /** date time format */
    const DATE_TIME_FORMAT = "Y-m-d H:i:s";

    /** date format */
    const DATE_FORMAT = "Y-m-d";

    /** accessor to all ical keys in the cache Cache::get(self::ICAL_ACCESSOR) */
    const ICAL_ACCESSOR = "icalcache";

    /** all published events of all locations */
    public function allPublicEvents()
    {
        $calendar = Cache::remember('icalAllPublicEvents', 4 * 60, function () {
            $now = new \DateTimeImmutable();
            $startDate = $now->sub(new \DateInterval("P2M"));
            $stopDate = $now->add(new \DateInterval("P6M"));

            $events = ClubEvent::where('evnt_date_start', ">=", $startDate->format(self::DATE_FORMAT))
                ->where('evnt_date_start', "<=", $stopDate->format(self::DATE_FORMAT))
                ->where('evnt_is_private', '=', '0')
                ->where('evnt_is_published', '=', '1')
                ->get();

            $vEvents = $events->map(function ($evt) {
                $vEvent = new Event();
                $vEvent->setOccurrence(
                    new TimeSpan(
                        new DateTime(\DateTime::createFromFormat(
                            self::DATE_TIME_FORMAT,
                            "" . $evt->evnt_date_start . " " . $evt->evnt_time_start
                        ), true),
                        new DateTime(
                            \DateTime::createFromFormat(
                                self::DATE_TIME_FORMAT,
                                "" . $evt->evnt_date_end . " " . $evt->evnt_time_end
                            ),
                            true
                        )
                    )
                );
                $vEvent->setSummary($evt->evnt_title);

                $eventLink = "" . URL::route('event.show', $evt->id);
                $eventTimeStart = substr($evt->evnt_time_start, 0, 5);
                $eventTimeEnd = substr($evt->evnt_time_end, 0, 5);

                $additionalInfo = $evt->evnt_public_info !== "" ? $evt->evnt_public_info : "(-)";

                $vEvent->setDescription("Link: " . $eventLink . "\n"
                    . "\n"
                    . trans('mainLang.begin') . ": " . $eventTimeStart . "\n"
                    . trans('mainLang.end') . ": " . $eventTimeEnd . "\n"
                    . "\n"
                    . trans('mainLang.additionalInfo') . ":\n"
                    . $additionalInfo . "\n");

                $section = $evt->section->title;
                $vEvent->setLocation(new Location($section));

                return $vEvent;
            });
            $vCalendar = new Calendar($vEvents->toArray());
            $vCalendar->addTimezone(new Timezone("Europe/Berlin"));

            $keys = Cache::get(self::ICAL_ACCESSOR, []);
            array_push($keys, "icalAllPublicEvents");
            $keys = array_unique($keys);
            Cache::put(self::ICAL_ACCESSOR, $keys, 4 * 60);

            return $vCalendar;
        });

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);
        return response($calendarComponent)
            ->withHeaders([
                'Content-Type' => 'text/calendar',
                'charset' => 'utf-8',
                'Content-Disposition' => 'attachment; filename="lara-public-feed-all-locations.ics"',
            ]);
    }


    /**
     * creates an iCal for all club-events of a chosen location
     * @param $location the location
     * @param $with_private_info if this is enabled private infos will appended to the result
     */
    public function events($location, $with_private_info = 0)
    {
        $calendar = Cache::remember("icalAllEvents" . $location, 4 * 60, function () use ($location, $with_private_info) {


            $now = new \DateTimeImmutable();
            $startDate = $now->sub(new \DateInterval("P2M"));
            $stopDate = $now->add(new \DateInterval("P6M"));

            $section = null;
            if ($with_private_info > 0) {
                $section = Section::where('section_uid', "=", $location)->first();
            } else {
                $section = Section::where('title', "=", $location)->first();
            }
            if (is_null($section)) {
                return new Calendar([]);
            }

            $eventsQuery = ClubEvent::where('evnt_date_start', ">=", $startDate->format(self::DATE_FORMAT))
                ->where('evnt_date_start', "<=", $stopDate->format(self::DATE_FORMAT))
                ->with('section', 'getSchedule')
                ->where('plc_id', "=", $section->id);

            if ($with_private_info == 0) {
                $eventsQuery = $eventsQuery->where("evnt_is_private", "=", '0')->where('evnt_is_published', '=', '1');
            }

            $events = $eventsQuery->get();
            $vEvents = $events->map(function ($evt) use ($location, $with_private_info) {
                $vEvent = new Event();
                $vEvent->setOccurrence(
                    new TimeSpan(
                        new DateTime(\DateTime::createFromFormat(
                            self::DATE_TIME_FORMAT,
                            "" . $evt->evnt_date_start . " " . $evt->evnt_time_start
                        ), true),
                        new DateTime(
                            \DateTime::createFromFormat(
                                self::DATE_TIME_FORMAT,
                                "" . $evt->evnt_date_end . " " . $evt->evnt_time_end
                            ),
                            true
                        )
                    )
                );
                $vEvent->setSummary($evt->evnt_title);

                $schedule = $evt->getSchedule;
                $start_time = "";

                $eventLink = "" . URL::route('event.show', $evt->id);
                $eventTimeStart = substr($evt->evnt_time_start, 0, 5);
                $eventTimeEnd = substr($evt->evnt_time_end, 0, 5);
                $preparationsTime = substr($schedule->schdl_time_preparation_start, 0, 5);
                $additionalInfo = $evt->evnt_public_info !== "" ? $evt->evnt_public_info : "(-)";

                $evtDescription = "Link: " . $eventLink . "\n"
                    . "\n"
                    . trans('mainLang.begin') . ": " . $eventTimeStart . "\n"
                    . trans('mainLang.end') . ": " . $eventTimeEnd . "\n"
                    . trans('mainLang.DV-Time') . ": " . $preparationsTime . "\n"
                    . "\n"
                    . "---\n"
                    . "\n"
                    . trans('mainLang.additionalInfo') . ":\n"
                    . $additionalInfo . "\n";

                if ($with_private_info > 0) {
                    $moreDetails = $evt->evnt_private_details !== "" ? $evt->evnt_private_details : "(-)";

                    $evtDescription = $evtDescription
                        . "\n"
                        . "---\n"
                        . "\n"
                        . trans('mainLang.moreDetails') . ":\n"
                        . $moreDetails;
                }

                $vEvent->setDescription($evtDescription);

                $section = $evt->section->title;
                $vEvent->setLocation(new Location($section));

                return $vEvent;
            });

            $vCalendar = new Calendar($vEvents->toArray());
            $vCalendar->addTimezone(new TimeZone("Europe/Berlin"));

            $keys = Cache::get(self::ICAL_ACCESSOR, []);
            array_push($keys, "icalAllEvents" . $location);
            $keys = array_unique($keys);
            Cache::put(self::ICAL_ACCESSOR, $keys, 4 * 60);

            return $vCalendar;
        });

        $filename = 'all-events-single-location';
        if ($with_private_info != 0) {
            $filename = $filename . "-with-private-info";
        }

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        return response($calendarComponent)
            ->withHeaders([
                'Content-Type' => 'text/calendar',
                'charset' => 'utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.ics"',
            ]);
    }


    /**
     * creates an individual iCal using your club_id
     * @param $prsn_uid - your club id, for example 56202a26bbe2c6a847ed83fe266b2017d8a21cf6db610886990dbbad2fdb1fd68b58e6209154a2358f925b670b98daaed403413f0f152f0522ff0f22e3b39b9c
     * @param $alarm - how many minutes you want to remind before the event
     */
    public function userScheduleWithAlarm($prsn_uid, $alarm = 0)
    {
        $personal_calendar = Cache::remember("ical" . $prsn_uid . $alarm, 4 * 60, function () use ($prsn_uid, $alarm) {
            /** @var $person Person*/
            $person = Person::query()->where('prsn_uid', '=', $prsn_uid)->first();

            if (isset($person)) {
                $user = $person->user;

                if (isset($user)) {
                    $userSettings = $user->settings;
                }

                if (isset($userSettings)) {
                    $userSettings->applyToSession();
                }
            }


            $shifts = Shift::query()->where('person_id', '=', $person->id)
                ->with("schedule", "schedule.event.section", "schedule.event", "type")
                ->get();

            $vEvents = $shifts->map(function (Shift $shift) use ($alarm) {
                $schedule = $shift->schedule;

                $start_time = "";
                $preparationNeeded = false;
                if ($schedule->event->evnt_time_start == $shift->start) {
                    $start_time = $schedule->schdl_time_preparation_start;
                    $preparationNeeded = true;
                } else {
                    $start_time = $shift->start;
                    $preparationNeeded = false;
                }

                $stopHour = intval(substr($shift->end, 0, 2));
                $stop_date = "";
                if ($stopHour > 18) {
                    $stop_date = $schedule->event->evnt_date_start;
                } else {
                    $stop_date = $schedule->event->evnt_date_end;
                }
                $startHour = intval(substr($shift->start, 0, 2));
                //stopdate -> detect date change
                if (($schedule->event->evnt_date_start != $schedule->event->evnt_date_end) && $startHour >= 0 && $startHour < $stopHour) {
                    $start_date = $stop_date;
                } else {
                    $start_date = $schedule->event->evnt_date_start;
                }

                $vEvent = new Event();

                $start_date_time = \DateTime::createFromFormat(
                    self::DATE_TIME_FORMAT,
                    "" . $start_date . " " . $start_time
                );
                $stop_date_time = \DateTime::createFromFormat(
                    self::DATE_TIME_FORMAT,
                    "" . $stop_date . " " . $shift->end
                );

                if ($start_date_time != false && $stop_date_time != false) {
                    $vEvent->setOccurrence(new TimeSpan(new DateTime($start_date_time, true), new DateTime($stop_date_time, true)));
                    $vEvent->setSummary("" . ($schedule->event->evnt_title) . " - " . ($shift->type->title()));

                    $eventLink = "" . URL::route('event.show', $schedule->event->id);
                    $eventTimeStart = substr($schedule->event->evnt_time_start, 0, 5);
                    $eventTimeEnd = substr($schedule->event->evnt_time_end, 0, 5);
                    $preparationsTime = substr($schedule->schdl_time_preparation_start, 0, 5);

                    $additionalInfo = $schedule->event->evnt_public_info !== "" ? $schedule->event->evnt_public_info : "(-)";
                    $moreDetails = $schedule->event->evnt_private_details !== "" ? $schedule->event->evnt_private_details : "(-)";

                    $vEvent->setDescription("Link: " . $eventLink . "\n"
                        . "\n"
                        . trans('mainLang.begin') . ": " . $eventTimeStart . "\n"
                        . trans('mainLang.end') . ": " . $eventTimeEnd . "\n"
                        . trans('mainLang.DV-Time') . ": " . $preparationsTime . "\n"
                        . "\n"
                        . trans('mainLang.shift') . ": " . $shift->type->title() . "\n"
                        . trans('mainLang.shiftTime') . ": " . substr($shift->start, 0, 5) . " - " . substr($shift->end, 0, 5) . "\n"
                        . "\n"
                        . "---\n"
                        . "\n"
                        . trans('mainLang.additionalInfo') . ":\n"
                        . $additionalInfo . "\n"
                        . "\n"
                        . "---\n"
                        . "\n"
                        . trans('mainLang.moreDetails') . ":\n"
                        . $moreDetails);

                    $section = $schedule->event->section->title;

                    $vEvent->setLocation(new Location($section));

                    if ($alarm > 0 && ($start_date_time > new \DateTime())) {
                        $action = new DisplayAction($schedule->event->evnt_title . " - " . ($shift->type->title()));

                        $trigger = new RelativeTrigger(\DateInterval::createFromDateString("-" . $alarm . "i"));
                        $vAlarm = new Alarm($action, $trigger);
                        $vEvent->addAlarm($vAlarm);
                    }
                }

                return $vEvent;
            });

            $vCalendar = new Calendar($vEvents->toArray());
            $vCalendar->addTimezone(new Timezone("Europe/Berlin"));

            $keys = Cache::get(self::ICAL_ACCESSOR, []);
            array_push($keys, "ical" . $prsn_uid . $alarm);
            $keys = array_unique($keys);
            Cache::put(self::ICAL_ACCESSOR, $keys, 4 * 60);

            return $vCalendar;
        });

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($personal_calendar);

        return response($calendarComponent)
            ->withHeaders([
                'Content-Type' => 'text/calendar',
                'charset' => 'utf-8',
                'Content-Disposition' => 'attachment; filename="personal-calendar.ics"',
            ]);
    }


    /**
     * creates an iCal for a single event
     * @param $evt_id id of the event that should be exported
     */
    public function singleEvent($evt_id)
    {
        $calendar = Cache::remember("icalevent" . $evt_id, 4 * 60, function () use ($evt_id) {

            $vEvents = [];
            $event = ClubEvent::where('id', '=', $evt_id)->first();
            if (isset($event)) {
                $vEvent = new Event();
                $vEvent->setSummary($event->evnt_title);
                $vEvent->setOccurrence(
                    new TimeSpan(
                        new DateTime(\DateTime::createFromFormat(
                            self::DATE_TIME_FORMAT,
                            "" . $event->evnt_date_start . " " . $event->evnt_time_start
                        ), true),
                        new DateTime(
                            \DateTime::createFromFormat(
                                self::DATE_TIME_FORMAT,
                                "" . $event->evnt_date_end . " " . $event->evnt_time_end
                            ),
                            true
                        )
                    )
                );

                $vEvent->setSummary($event->evnt_title);

                $eventLink = "" . URL::route('event.show', $event->id);
                $eventTimeStart = substr($event->evnt_time_start, 0, 5);
                $eventTimeEnd = substr($event->evnt_time_end, 0, 5);
                $additionalInfo = $event->evnt_public_info !== "" ? $event->evnt_public_info : "(-)";

                $vEvent->setDescription("Link: " . $eventLink . "\n"
                    . "\n"
                    . trans('mainLang.begin') . ": " . $eventTimeStart . "\n"
                    . trans('mainLang.end') . ": " . $eventTimeEnd . "\n"
                    . "\n"
                    . trans('mainLang.additionalInfo') . ":\n"
                    . $additionalInfo . "\n");

                $section = $event->section->title;
                $location = new Location($section);
                $vEvent->setLocation($location);
                array_push($vEvents, $vEvent);
            }

            $vCalendar = new Calendar($vEvents);
            $vCalendar->addTimezone(new Timezone("Europe/Berlin"));

            $keys = Cache::get(self::ICAL_ACCESSOR, []);
            array_push($keys, "icalevent" . $evt_id);
            $keys = array_unique($keys);
            Cache::put(self::ICAL_ACCESSOR, $keys, 4 * 60);

            return $vCalendar;
        });
        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);
        return response($calendarComponent)
            ->withHeaders([
                'Content-Type' => 'text/calendar',
                'charset' => 'utf-8',
                'Content-Disposition' => 'attachment; filename="lara-event-' . $evt_id . '.ics"',
            ]);
    }


    /** generate links for ui */
    public function generateLinks()
    {
        $user = Auth::user();

        $userId = $user->person->prsn_ldap_id;

        $person = Person::where('prsn_ldap_id', '=', $userId)->first();

        $result = [];

        $sections = Section::where('title', '<>', '-')->get();

        $result['allPublicEvents'] = URL::route('icalallevents');

        foreach ($sections as $section) {

            if ($user) {
                $sectionLink = [$section->title => URL::to('/') . '/ical/feed/' . $section->section_uid . "/1"];
                $result['location'][] = $sectionLink;
            }
            $publicLinks = [$section->title => URL::to('/') . "/ical/location/" . $section->title];

            $result['locationPublic'][] = $publicLinks;
            $result['locationName'][] = $section->title;
        }

        if (!$user) {
            $result['isPublic'] = true;
        } else {
            $result['isPublic'] = false;
        }

        if (!is_null($person) && $user) {
            $result['personal'] = URL::to('/') . '/ical/events/user/' . $person->prsn_uid . '/';
        }

        return response()->json($result);
    }


    /**
     * Will change the publishing state of a given event to the opposite
     * @param  int $id for event ID
     * @return Redirect back
     */
    public function togglePublishState($id)
    {

        // Find event
        $event = ClubEvent::findOrFail($id);

        // Check credentials: you can only delete, if you have rights for marketing or management.
        $user = Auth::user();

        if (!$user || !$user->isAn(RoleUtility::PRIVILEGE_MARKETING, RoleUtility::PRIVILEGE_ADMINISTRATOR, RoleUtility::PRIVILEGE_CL)) {
            session()->put(
                'message',
                'Du darfst dieses Event nicht veröffentlichen! Frage die Clubleitung oder Markleting ;)'
            );
            session()->put('msgType', 'danger');

            return back();
        }

        if ($event->evnt_is_published == 1) {
            // was published, intent: unpublish

            $event->evnt_is_published = 0;
            $event->save();
            Utilities::clearIcalCache();

            // Log the action while we still have the data
            Log::info('Event unpublished: ' . $user->name . ' (' . $user->person->prsn_ldap_id . ', '
                . ') unpublished event "' . $event->evnt_title . '" (eventID: ' . $event->id . ') on ' . $event->evnt_date_start . '.');

            // Inform the user
            session()->put('message', "Dieses Event wurde erfolgreich aus dem Kalenderfeed entfernt.");
            session()->put('msgType', 'danger');

            return back();

        } else {
            // was unpublished, intent: publish

            $event->evnt_is_published = 1;
            $event->save();
            Utilities::clearIcalCache();

            // Log the action while we still have the data
            Log::info('Event published: ' . $user->name . ' (' . $user->person->prsn_ldap_id . ', '
                . $user->roles()->get(['name']) . ') published event "' . $event->evnt_title . '" (eventID: ' . $event->id . ') on ' . $event->evnt_date_start . '.');

            // Inform the user
            session()->put('message', "Dieses Event wurde erfolgreich zum Kalenderfeed hinzugefügt.");
            session()->put('msgType', 'success');

            return back();
        }
    }

}
