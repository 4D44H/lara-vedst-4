<?php

namespace Lara\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Input;
use Lara\Club;
use Lara\Logging;
use Lara\Person;
use Lara\Shift;
use Lara\ShiftType;
use Lara\Status;
use Lara\Utilities;
use Lara\utilities\RoleUtility;


class ShiftController extends Controller
{
    /**
     * Display the specified resource.
     * Returns JSON-formated contents of a shift.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shift = Shift::where('id', '=', $id)
            ->with('type', 'getPerson.getClub')
            ->firstOrFail();

        // Person NULL means "=FREI=" - check for it every time you query a relationship
        $ldapId = !is_null($shift->getPerson) ? $shift->getPerson->prsn_ldap_id : "";
        $name = !is_null($shift->getPerson) ? $shift->getPerson->prsn_name : "=FREI=";
        $status = !is_null($shift->getPerson) ? $shift->getPerson->prsn_status : "";
        $clubTitle = !is_null($shift->getPerson) ? $shift->getPerson->getClub->clb_title : "";
        $isCurrentUser = $ldapId == Auth::user()->person->prsn_ldap_id;
        $response = [
            'id' => $shift->id,
            'title' => $shift->type->title(),
            'prsn_name' => $name,
            'prsn_ldap_id' => $ldapId,
            'prsn_status' => $status,
            'clb_title' => $clubTitle,
            'comment' => $shift->comment,
            'start' => $shift->start,
            'end' => $shift->end,
            'optional' => $shift->optional,
            'updated_at' => $shift->updated_at,
            'is_current_user' => $isCurrentUser
        ];

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     * Changes contents of the shift specified by ID to contents in the REQUEST
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Check if it's our form (CSRF protection)
        if (session()->token() !== $request->input('_token')) {
            return response()->json(["errorCode" => 'sessionExpired'], 401);
        }

        Utilities::clearIcalCache();

        // If we only want to modify the shiftType via management pages - do it without evaluating the rest
        if (!empty($request->input('shiftTypeId')) && is_numeric($request->input('shiftTypeId'))) {

            // Find the corresponding shift object
            /** @var Shift $shift */
            $shift = Shift::query()->where('id', '=', $request->input('entryId'))->with('schedule')->with('schedule.event')
                ->first();

            // Substitute values
            $shift->shifttype_id = $request->input('shiftTypeId');
            $shift->save();

            // Log changes
            Logging::logShiftRevision($shift, "revisions.shiftTypeChanged");

            // Formulate the response
            return response()->json([
                "entryId" => $shift->id,
                "updatedShiftTypeTitle" => ShiftType::where('id', '=', $shift->shifttype_id)->first()->title
            ]);
        }

        // Extract request data
        $shiftId = $request->input('entryId');
        $userName = $request->input('userName');
        $ldapId = $request->input('ldapId');
        $timestamp = $request->input('timestamp');
        $userClub = $request->input('userClub');
        $userComment = $request->input('userComment');
        $password = $request->input('password');

        // Check if someone modified LDAP ID manually
        if (!empty($ldapId) && !is_numeric($ldapId)) {
            return response()->json("Fehler: die Clubnummer wurde in falschem Format angegeben. Bitte versuche erneut oder melde diesen Fehler dem Admin.", 400);
        }

        // Find the corresponding shift object
        $shift = Shift::where('id', '=', $shiftId)->first();

        // Remember old value for logging
        $oldPerson = $shift->getPerson;
        $oldComment = $shift->comment;

        // Check if that schedule needs a password and validate hashes
        if ($shift->getSchedule->schdl_password !== ''
            && !Hash::check($password, $shift->getSchedule->schdl_password)) {
            return response()->json("Fehler: das angegebene Passwort ist falsch, keine Änderungen wurden gespeichert. Bitte versuche erneut oder frage einen anderen Mitglied oder CL.", 401);
        }
        $isAllowedToChange = $this->isAllowedToChange($shift);
        // check if event is blocked by date
        if (!is_null($shift->schedule->event->unlock_date) && Carbon::now()->isBefore($shift->schedule->event->unlock_date) && !$isAllowedToChange) {
            return response()->json("Fehler: Die Veranstaltung ist noch nicht freigeschaltet.", 401);
        }

        // Control if the updated_at matches with the request timestamp
        if ($timestamp <> $shift->updated_at) {
            if (!is_null($shift->getPerson()->first()) || $oldComment <> $userComment) {
                // Find user status icon parameters to return
                $userStatus = $this->updateStatus($shift);

                // Formulate the response
                $person = is_null($shift->getPerson()->first()) ? null : $shift->getPerson()->first();
                $prsn_ldap_id = $person ? $person->prsn_ldap_id : null;
                $isCurrentUser = !is_null(Auth::user()) && $prsn_ldap_id == Auth::user()->person->prsn_ldap_id;
                return response()->json([
                    "errorCode" => "error_outOfSync",
                    "entryId" => $shift->id,
                    "userStatus" => $userStatus,
                    "userName" => $person ? $person->prsn_name : null,
                    "ldapId" => $prsn_ldap_id,
                    "userClub" => $person ? $person->getClub->clb_title : null,
                    "userComment" => $shift->comment,
                    "timestamp" => $shift->updated_at->toDateTimeString(),
                    "is_current_user" => $isCurrentUser
                ], 409);

            }
        }
        // FYI:
        // We separate schedule shift person change from comment change
        // because we need an option to add a comment to an empty field.
        // Deleting or editing the person doesn't delete the comment.

        // Check for person change (we will check for comment change later):
        //
        // Case EMPTY:     Entry was empty, shift is empty now              -> do nothing
        // Case SAME:      Entry was not empty, but same person is there    -> do nothing
        // Case ADDED:     Entry was empty, new data entered                -> add new data
        // Case DELETED:   Entry was not empty, shift is empty now          -> delete old data
        // Case CHANGED:   Entry was not empty, new name entered            -> delete old data, then add new data


        if (!isset($shift->person_id)) {
            if (!$userName == '') {
                // Case ADDED:   Shift was empty, new data entered -> add new data
                $this->onAdd($shift, $userName, $ldapId, $userClub);
                Logging::shiftChanged($shift, $oldPerson, $shift->person);
            }
        } else {
            if ($userName == '') {
                // Case DELETED: Shift was not empty, shift is empty now -> delete old data
                $this->onDelete($shift);
                Logging::shiftChanged($shift, $oldPerson, $shift->person);
            } else {
                // Differentiate between shifts with members or with guests
                if (!is_null($shift->getPerson()->first()->prsn_ldap_id)) {
                    // Member shifts (with LDAP ID provided) shouldn't change club id, so no need to do anything in that case either
                    if ($shift->getPerson->prsn_name == $userName
                        && Person::where('id', '=', $shift->person_id)->first()->prsn_ldap_id == $ldapId) {
                        // Possibility 1: same name, same ldap = same person
                        // Case SAME: Shift was not empty, but same person is there -> do nothing
                    } else {
                        // Possibility 2: same name, new/empty ldap  = another person
                        // Possibility 3: new name,  same ldap       = probably LDAP ID not cleared on save, assume another person
                        // Possibility 4: new name,  new/empty ldap  = another person
                        // Case CHANGED: Shift was not empty, new data entered -> delete old data, then add new data
                        $this->onDelete($shift);
                        $this->onAdd($shift, $userName, $ldapId, $userClub);
                        Logging::shiftChanged($shift, $oldPerson, $shift->person);
                    }
                } else {
                    // Guest shifts may change club
                    if ($shift->getPerson->prsn_name == $userName
                        && $shift->getPerson->getClub->clb_title == $userClub
                        && $ldapId == '') {
                        // Possibility 1: same name, same club, empty ldap  = do nothing
                        // Case SAME: Shift was not empty, but same person is there -> do nothing
                    } else {
                        // Possibility 2: same name, new club,  empty ldap  -> Case CHANGED
                        // Possibility 3: same name, same club, new ldap    -> Case CHANGED
                        // Possibility 4: same name, new club,  new ldap    -> Case CHANGED
                        // Possibility 5: new name,  same club, empty ldap  -> Case CHANGED
                        // Possibility 6: new name,  new club,  empty ldap  -> Case CHANGED
                        // Possibility 7: new name,  same club, new ldap    -> Case CHANGED
                        // Possibility 8: new name,  new club,  new ldap    -> Case CHANGED
                        // Case NAME CHANGED: Shift was not empty, new data entered -> delete old data, then add new data
                        $this->onDelete($shift);
                        $this->onAdd($shift, $userName, $ldapId, $userClub);

                        Logging::shiftChanged($shift, $oldPerson, $shift->person);
                    }
                }
            }
        }

        // Now let's check for comment changes:
        //
        // Case EMPTY:   Comment was empty, comment is empty now                -> do nothing
        // Case SAME:    Comment was not empty, but same comment is there       -> do nothing
        // Case ADDED:   Comment was empty, new comment entered                 -> add new data
        // Case DELETED: Comment was not empty, comment is empty now            -> delete old data
        // Case CHANGED: Comment was not empty, new comment entered             -> delete old data, then add new data
        if (empty($shift->comment)) {
            if (!$userComment == '') {
                // Case ADDED: Comment was empty, new comment entered -> add new data
                $shift->comment = $userComment;
                $shift->save();
                Logging::commentAdded($shift, $userComment);
            } else {
                //Case EMPTY: Comment was empty, comment is empty now -> do nothing
            }
        } else {
            if ($shift->comment !== $userComment) {
                if ($userComment == '') {
                    // Case DELETED: Comment was not empty, comment is empty now -> delete old data
                    $shift->comment = null;
                    $shift->save();
                    Logging::commentDeleted($shift, $oldComment);
                } else {
                    // Case CHANGED: Comment was not empty, new comment entered -> delete old data, then add new data
                    $shift->comment = $userComment;
                    $shift->save();
                    Logging::commentChanged($shift, $oldComment, $userComment);
                }
            }
        }

        // Find user status icon parameters to return
        $userStatus = $this->updateStatus($shift);

        // Formulate the response
        $prsn_ldap_id = is_null($shift->getPerson()->first()) ? "" : $shift->getPerson()->first()->prsn_ldap_id;
        $user = Auth::user();
        return response()->json([
            "entryId" => $shift->id,
            "userStatus" => $userStatus,
            "userName" => is_null($shift->getPerson()->first()) ? "" : $shift->getPerson()->first()->prsn_name,
            "ldapId" => $prsn_ldap_id,
            "userClub" => $userClub,
            "userComment" => $shift->comment,
            "timestamp" => $shift->updated_at->toDateTimeString(),
            "is_current_user" => $prsn_ldap_id == ($user ? $user->person->prsn_ldap_id : NULL)
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     * Deletes the dataset in table Person if it's a guest (LDAP id = NULL), but doesn't touch club members.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        $shift = Shift::find($id);
        self::delete($shift);
    }

    /**
     * Deletes the shift from the database
     * @param \Lara\Shift $shift
     */
    public static function delete($shift)
    {

        // Check if shift exists
        if (is_null($shift)) {
            session()->put('message', 'Fehler: Löschvorgang abgebrochen - der Dienstplaneintrag existiert nicht.');
            session()->put('msgType', 'danger');
            return back();
        }
        Utilities::clearIcalCache();

        // Delete the shift
        $shift->delete();
    }

    /**
     * @param $schedule
     * @param $isNewEvent
     * @param $title
     * @param $id
     * @param $type
     * @param $start
     * @param $end
     * @param $weight
     * @param $position
     */
    public static function makeShift($schedule, $isNewEvent, $title, $id, $type, $start, $end, $weight, $position, $optional)
    {
        if ($title === "") {
            return;
        }
        // If no id is set, create a new Model
        $shift = self::createShiftsFromEditSchedule($id, $title, $type, $start, $end, $weight, $position,
            $schedule->id, $optional);

        $shift->save();
    }

    /**
     * @param $title
     * @param $id
     * @param $type
     * @param $start
     * @param $end
     * @param $weight
     * @param $position
     * @param $optional
     * @param $scheduleId
     * @return Shift
     */
    public static function createShiftsFromEditSchedule($id, $title, $type, $start, $end, $weight, $position, $scheduleId = null, $optional = false)
    {

        if ($title === "") {
            return;
        }

        $shift = Shift::firstOrNew(["id" => $id]);

        // If there was a shifttype passed and one with the correct title exists, use this one
        // Otherwise create a new model
        $oldShiftType = $shift->type;

        // we need a raw statement for case sensitivity
        $shiftType = ShiftType::whereRaw("BINARY `title`= ?", $title)
            ->where(function ($query) use ($type, $start, $end) {
                $query->where('id', $type);
                $query->orWhere('start', $start);
                $query->where('end', $end);
            })
            ->first();
        if (is_null($shiftType)) {
            $shiftType = new ShiftType([
                "id" => $type,
                "title" => $title,
                'start' => $start,
                'end' => $end,
                'statistical_weight' => $weight,
            ]);
            $shiftType->save();
        }

        // if the model was newly created, save the new shiftType
        $shift->fill([
            "start" => $start,
            "end" => $end,
            "statistical_weight" => $weight,
            "shifttype_id" => $shiftType->id,
            "position" => $position,
            'optional' => $optional,
            "schedule_id" => $scheduleId
        ]);

        if ($shift->exists) {
            if ($shift->isDirty('shifttype_id')) {
                Logging::shiftTypeChanged($shift, $oldShiftType, $shiftType);
            }

            if ($shift->isDirty('statistical_weight')) {
                Logging::shiftStatisticalWeightChanged($shift);
            }

            if ($shift->isDirty('start')) {
                Logging::shiftStartChanged($shift);
            }

            if ($shift->isDirty('end')) {
                Logging::shiftEndChanged($shift);
            }

            if ($shift->isDirty('optional')) {
                Logging::shiftOptionalChanged($shift);
            }
        } else {
            if (!is_null($scheduleId)) {
                Logging::shiftCreated($shift);
            }
        }
        $shift->save();
        return $shift;
    }



//--------- PRIVATE FUNCTIONS ------------


    /**
     * Deletes a shift.
     * Deletes the dataset in table Person if it's a guest (LDAP id = NULL), but doesn't touch club members.
     *
     * @param Shift $shift
     * @return void
     */
    private function onDelete($shift)
    {
        if (!isset($shift->getPerson->prsn_ldap_id)) {
            Person::destroy($shift->person_id);
        }

        $shift->person_id = null;
        $shift->save();
    }


    /**
     * Adds new person to the shift.
     *
     * @param Shift $shift
     * @param String $userName
     * @param int $ldapId
     * @param String $userClub
     * @return void
     */
    private function onAdd($shift, $userName, $ldapId, $userClub)
    {
        // If no LDAP id provided - create new GUEST person
        if ($ldapId == '') {
            $person = Person::create(array('prsn_ldap_id' => null));
            $person->prsn_name = $userName;
            $person->prsn_status = "";
        } // Otherwise find existing MEMBER person in DB
        else {
            $person = Person::where('prsn_ldap_id', '=', $ldapId)->first();

            // If not found, then a user is adding own data for the first time.
            // Let's create a new person with data provided in the session.
            $user = Auth::user();

            if (is_null($person)) {
                $person = Person::create(array('prsn_ldap_id' => $ldapId));
                $person->prsn_name = $userName;
                $person->prsn_status = $user->status;
                $person->prsn_uid = hash("sha512", uniqid());
            }

            // If a person adds him/herself - update status from session to catch if it was changed in LDAP
            if ($person->prsn_ldap_id == $user->person->prsn_ldap_id) {
                $person->prsn_status = $user->status;
                $person->prsn_name = $user->name;
            }

        }

        // If club input is empty setting clubId to '-' (clubId 1).
        // Else - look for a match in the Clubs DB and set person->clubId = matched club's id.
        // No match found - creating a new club with title from input.
        if ($userClub == '' || $userClub == '-') {
            $person->clb_id = '1';
        } else {
            $match = Club::firstOrCreate(array('clb_title' => $userClub));
            $person->clb_id = $match->id;
        }

        // Save changes to person and schedule shift
        $person->updated_at = Carbon::now();
        $person->save();

        $shift->person_id = $person->id;
        $shift->save();
    }


    /**
     * Checks what kind of person occupies shift after changes and sets the status
     * to "free" or a person userStatus accordingly
     *
     * @param Shift $shift
     * @return array $userStatus
     */
    private function updateStatus($shift)
    {
        if (!is_null($shift->person_id)) {
            return Status::style($shift->person->prsn_status);
        }
        return ["status" => "fa fa-question", "style" => "color:lightgrey;", "title" => "Dienst frei"];
    }

    /**
     * @param $shift
     * @return bool
     */
    public function isAllowedToChange($shift): bool
    {
        $clubEvent = $shift->schedule->event;
        $createPersonLdapId = $clubEvent->creator ? $clubEvent->creator->person->prsn_ldap_id : null;
        return \Auth::hasUser() && (\Auth::user()->hasPermissionsInSection($clubEvent->section, RoleUtility::PRIVILEGE_CL) || Person::isCurrent($createPersonLdapId));
    }


}
