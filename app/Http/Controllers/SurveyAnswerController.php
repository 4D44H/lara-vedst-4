<?php

namespace Lara\Http\Controllers;

use Auth;
use Hash;
use Illuminate\Http\Request;
use Input;
use Lara\Library\Revision;
use Lara\QuestionType;
use Lara\Status;
use Lara\Survey;
use Lara\SurveyAnswer;
use Lara\SurveyAnswerCell;



/**
 * Class SurveyAnswerController
 * @package Lara\Http\Controllers
 *
 * RESTful Resource Controller, implements only 3 RESTful actions (store, update, destroy)
 */
class SurveyAnswerController extends Controller
{
    /**
     * SurveyAnswerController constructor.
     * call middleware to give only authenticated users access to the methods
     */
    public function __construct()
    {
        // if survey is private, reject guests
        $this->middleware('privateEntry:Lara\Survey,survey');
        // only Ersteller/Admin/Marketing/Clubleitung, privileged user groups only
        $this->middleware('creator:Lara\SurveyAnswer,answer', ['only' => ['update', 'destroy']]);
        // after deadline, only Ersteller/Admin/Marketing/Clubleitung, privileged user groups only
        $this->middleware('deadlineSurvey', ['only' => ['store', 'update', 'destroy']]);
    }

    /**
     * @param int $surveyid
     * @param Request $input
     * @return Redirect;
     */
    public function store($surveyid, Request $input)
    {
        $survey = Survey::findOrFail($surveyid);

        //check if survey needs a password and validate hashes
        if ($survey->password !== ''
            && !Hash::check($input->password, $survey->password)) {
            session()->put('message', 'Fehler: das angegebene Passwort ist falsch, keine Änderungen wurden gespeichert. Bitte versuche es erneut oder frage ein anderes Mitglied oder CL.');
            session()->put('msgType', 'danger');
            return redirect()->action([SurveyController::class, 'show'], [$survey->id]);
        }

        $survey_answer = new SurveyAnswer();
        $revision_answer = new Revision($survey_answer);
        // prevent guestentries with ldapId
        // prevent entries with foreign usernames but valid ldap_id
        $user = Auth::user();
        if ($user && $user->name === $input->name) {
            $survey_answer->creator_id = $user->person->id;
        } else {
            $survey_answer->creator_id = null;
        }

        $survey_answer->survey_id = $surveyid;
        $survey_answer->name = $input->name;
        $survey_answer->club = $input->club;
        $survey_answer->save();
        $revision_answer->save($survey_answer, "Antwort erstellt");

        $questions = $survey->getQuestions;

        foreach ($questions as $key => $question) {
            $survey_answer_cell = new SurveyAnswerCell();
            $revision_cell = new Revision($survey_answer_cell);
            $survey_answer_cell->survey_question_id = $question->id;
            $survey_answer_cell->survey_answer_id = $survey_answer->id;
            switch ($question->field_type) {
                case QuestionType::Text :
                    $survey_answer_cell->answer = $input->answers[$key];
                    break;
                case QuestionType::Checkbox :
                    if ($input->answers[$key] == -1) {
                        $survey_answer_cell->answer = "surveys.noAnswer";
                    } elseif ($input->answers[$key] == 0) {
                        $survey_answer_cell->answer = "surveys.no";
                    } elseif ($input->answers[$key] == 1) {
                        $survey_answer_cell->answer = "surveys.yes";
                    }
                    break;
                case QuestionType::Dropdown :
                    if ($input->answers[$key] == -1) {
                        $survey_answer_cell->answer = "surveys.noAnswer";
                    } else {
                        $survey_answer_cell->answer = $input->answers[$key];
                    }
                    break;
            }
            $survey_answer_cell->save();
            $revision_cell->save($survey_answer_cell);
        }
        session()->put('message', 'Erfolgreich abgestimmt!');
        session()->put('msgType', 'success');

        return redirect()->action([SurveyController::class, 'show'], [$survey->id]);
    }

    /**
     * @param int $surveyid
     * @param int $answerid
     * @param Request $input
     * @return json
     */
    public function update($surveyid, $answerid)
    {
        /** @var Request $input */
        $input = request();
        //validate session token
        if (session()->token() !== $input->get('_token')) {
            return response()->json('Fehler: die Session ist abgelaufen. Bitte aktualisiere die Seite und logge dich ggf. erneut ein.', 401);
        }

        $survey = Survey::findOrFail($surveyid);

        //check if survey needs a password and validate hashes
        if ($survey->password !== ''
            && !Hash::check($input->password, $survey->password)) {
            return response()->json('Fehler: das eingegebene Passwort war leider falsch.', 401);
        }

        //give a reminder to fill out required fields
        if ($input->error[0] == "required_missing") {
            return response()->json('Fehler: Es wurden nicht alle Pflichtfragen beantwortet.', 401);
        }

        $survey_answer = SurveyAnswer::findOrFail($answerid);
        $revision_answer = new Revision($survey_answer);
        // prevent guestentries with ldapId
        (Auth::user()) ? ($survey_answer->creator_id = Auth::user()->person->id) : ($survey_answer->creator_id = null);
        $survey_answer->survey_id = $surveyid;
        $survey_answer->name = $input->name;
        $survey_answer->club = $input->club;
        $survey_answer->save();
        $revision_answer->save($survey_answer, "Antwort geändert");

        $questions = $survey->getQuestions;
        $answer_cells = $survey_answer->getAnswerCells;

        foreach ($questions as $key => $question) {
            $survey_answer_cell = SurveyAnswerCell::find($answer_cells->get($key)->id);
            $revision_cell = new Revision($survey_answer_cell);
            $survey_answer_cell->survey_question_id = $question->id;
            $survey_answer_cell->survey_answer_id = $survey_answer->id;
            switch ($question->field_type) {
                case 1: //Freitext
                    $survey_answer_cell->answer = $input->answers[$key];
                    break;
                case 2: //Checkbox (Ja/Nein)
                    if ($input->answers[$key] == -1) {
                        $survey_answer_cell->answer = "keine Angabe";
                    } elseif ($input->answers[$key] == 0) {
                        $survey_answer_cell->answer = "Nein";
                    } elseif ($input->answers[$key] == 1) {
                        $survey_answer_cell->answer = "Ja";
                    }
                    break;
                case 3: //Dropdown
                    if ($input->answers[$key] == -1) {
                        $survey_answer_cell->answer = "keine Angabe";
                    } else {
                        $survey_answer_cell->answer = $input->answers[$key];
                    }
                    break;
            }
            $survey_answer_cell->save();
            $revision_cell->save($survey_answer_cell);
        }

        $creator = $survey_answer->getPerson;
        (!empty($creator)) ? ($user_status = $creator->prsn_status) : ($user_status = null);

        $user_status = $this->updateStatus($user_status);
        return response()->json(["user_status" => $user_status
        ],
            200);
    }

    /**
     * @param int $surveyid
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($surveyid, $id, Request $input)
    {

        $survey = Survey::findOrFail($surveyid);
        if ($survey->password !== ''
            && !Hash::check($input->password, $survey->password)) {
            session()->put('message', 'Fehler: das eingegebene Passwort war leider falsch.');
            session()->put('msgType', 'error');
            return redirect()->action([SurveyController::class, 'show'], [$surveyid]);
        }
        $answer = SurveyAnswer::findOrFail($id);
        $revision_answer = new Revision($answer);

        foreach ($answer->getAnswerCells as $cell) {
            //delete the AnswerCells
            $revision_cell = new Revision($cell);
            $cell->delete();
            $revision_cell->save($cell);
        }

        // Now delete the SurveyAnswer itself
        $answer->delete();
        $revision_answer->save($answer, "Antwort gelöscht");

        session()->put('message', 'Erfolgreich gelöscht!');
        session()->put('msgType', 'success');

        return redirect()->action([SurveyController::class, 'show'], [ $surveyid ]);
    }

    /*
     *  -------------------- END OF REST-CONTROLLER --------------------
     */


    /**
     * returns the the style (colored dot) for the corresponding status
     * used in ajax requests
     * @param string $user_status
     * @return array
     */
    private function updateStatus($user_status)
    {
        return Status::style($user_status);
    }
}

