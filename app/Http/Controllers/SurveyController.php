<?php

namespace Lara\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Hash;
use Lara\Library\Revision;
use Lara\Person;
use Lara\RevisionEntry;
use Lara\SurveyAnswerCell;
use Lara\Utilities;
use Lara\utilities\RoleUtility;
use DateTime;
use Lara\Survey;
use Lara\QuestionType;
use Lara\SurveyQuestion;
use Lara\SurveyAnswer;
use Lara\SurveyAnswerOption;
use Lara\Club;
use Lara\Http\Requests\SurveyRequest;
use Carbon\Carbon;

/**
 * Class SurveyController
 * @package Lara\Http\Controllers
 *
 * RESTful Resource Controller, implements all RESTful actions (index, create, store, show, edit, update, destroy)
 */
class SurveyController extends Controller
{
    /**
     * SurveyController constructor.
     * call middleware to give only authenticated users access to the methods
     */
    public function __construct()
    {
        // reject guests
        $this->middleware('rejectGuests', ['only' => 'create', 'store']);
        // if survey is private, reject guests
        $this->middleware('privateEntry:Lara\Survey,survey', ['except' => ['create', 'store']]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        //prepare correct date and time format to be used in forms for deadline
        $datetime = carbon::now()->endOfDay();
        $time = $datetime->toTimeString();
        $date = $datetime->toDateString();
        //placeholder because createSurveyView needs variable, can set defaults here
        $survey = new Survey();
        $isEdit = false;
        return view('createSurveyView', compact('survey', 'time', 'date', 'isEdit'));
    }

    /**
     * @param SurveyRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(SurveyRequest $request)
    {
        $survey = new Survey;
        $revision_survey = new Revision($survey);
        $survey->makeFromRequest($request);

        $survey->save();
        $revision_survey->save($survey, "Umfrage erstellt");

        // values will reindex the array with consecutive integers, removing holes
        // (e.g. if a user deletes a question somewhere in the middle)
        $questions = collect($request->questionText)->values();
        $answerOptions = collect($request->answerOption)->values();

        // convert textInput to int for better processing
        $types = collect($request->type_select)
            ->map(function ($type) {
                return intval($type);
            })
            ->values();

        $required = $request->required ? collect($request->required)->values() : $questions->map(function () {
            return false;
        });

        //make new question model instance, fill it and save it

        $questionsParameters = $questions->zip($types, $required, $answerOptions);
        foreach ($questionsParameters as $order => list($question, $type, $isRequired, $options)) {
            SurveyQuestion::make($survey, $order, $question, $type, $isRequired, $options);
        }

        return redirect()->action([SurveyController::class, 'show'], [$survey->id]);
    }


    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        //find SurveyID
        $survey = Survey::findorFail($id);

        $this->authorize('delete', $survey);

        foreach ($survey->answers as $answer) {
            foreach ($answer->cells as $cell) {
                Revision::deleteWithRevision($cell);
            }
            Revision::deleteWithRevision($answer);
        }

        foreach ($survey->questions as $question) {
            foreach ($question->options as $answerOption) {
                Revision::deleteWithRevision($answerOption);
            }
            Revision::deleteWithRevision($question);
        }

        Revision::deleteWithRevision($survey);

        session()->put('message', 'Umfrage gelöscht!');
        session()->put('msgType', 'success');

        return redirect()->action([MonthController::class, 'currentMonth']);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {


        //find survey
        $survey = Survey::findOrFail($id);

        $user = Auth::user();
        if (!$user && $survey->is_private == 1)
        {
            session()->put('message', config('messages_de.access-denied'));
            session()->put('msgType', 'danger');
            return redirect()->action([MonthController::class, 'showMonth'], array('year' => date('Y'),
                                                                       'month' => date('m')));
        }

        //find questions
        $questions = $survey->questions;
        $questionCount = count($questions);
        //find answers
        $answers = $survey->answers;
        //find all clubs
        $clubs = Club::all();

        //get the information from the current session
        $user = Auth::user();
        if($user == null){
            $userId = null;
            $userStatus = null;
            $username = null;
            $ldapid = null;
        } else {
            $userId = Auth::user()->person->id;
            $userStatus = Auth::user()->status;
            $username = Auth::user()->name;
            $ldapid = Auth::user()->person->prsn_ldap_id;
        }
        $userParticipatedAlready = $survey->answers
            ->contains(function ($answer) use ($userId) {
                return $answer && $answer->creator_id === $userId && !empty($userId);
            });

        $answers_with_trashed_ids = SurveyAnswer::withTrashed()
            ->where('survey_id', $survey->id)
            ->get()
            ->map(function ($answer) {
                return $answer->id;
            });

        $revisions = \Lara\Revision::join("revision_object_relations", "revisions.id", "=",
            "revision_object_relations.revision_id")
            ->where("revision_object_relations.object_name", "=", "SurveyAnswer")
            ->whereIn("revision_object_relations.object_id", $answers_with_trashed_ids)
            ->orWhere(function ($query) use ($survey) {
                $query->where("revision_object_relations.object_name", "=", "Survey")
                    ->where("revision_object_relations.object_id", "=", $survey->id);
            })
            ->distinct()
            ->orderBy("created_at", "desc")
            ->get(['creator_id', 'summary', 'created_at', 'revision_id'])->toArray();

        foreach ($revisions as &$revision) {
            $creator = Person::where('id', '=', $revision['creator_id'])
                ->get(['prsn_name'])
                ->first();
            $isGuest = empty($creator) || is_null($revision['creator_id']);
            $revision['creator_name'] = $isGuest ? trans('guest') : $creator->prsn_name;
            unset($revision['creator_id']);
            $revision['revision_entries'] = RevisionEntry::where('revision_id', '=', $revision['revision_id'])
                ->get(['changed_column_name', 'old_value', 'new_value'])
                ->toArray();
            unset($revision['revision_id']);

            foreach ($revision['revision_entries'] as &$entry) {
                // rename the displayed column names to hide database-schema
                switch ($entry['changed_column_name']) {
                    case "name":
                        $entry['changed_column_name'] = "Name";
                        break;
                    case "club":
                        $entry['changed_column_name'] = "Club";
                        break;
                    case "answer":
                        $entry['changed_column_name'] = "Antwort";
                        break;
                    case "title":
                        $entry['changed_column_name'] = "Titel";
                        break;
                    case "description":
                        $entry['changed_column_name'] = "Beschreibung";
                        break;
                    case "deadline":
                        $entry['changed_column_name'] = "Deadline";
                        break;
                    case "is_anonymous":
                        $entry['changed_column_name'] = "Ergebnisse sind nur für den Umfragenersteller sichtbar?";
                        $entry['old_value'] = $this->booleanIntoText($entry['old_value']);
                        $entry['new_value'] = $this->booleanIntoText($entry['new_value']);
                        break;
                    case "show_results_after_voting":
                        $entry['changed_column_name'] = "Ergebnisse sind erst nach dem Ausfüllen sichtbar?";
                        $entry['old_value'] = $this->booleanIntoText($entry['old_value']);
                        $entry['new_value'] = $this->booleanIntoText($entry['new_value']);
                        break;
                    case "is_private":
                        $entry['changed_column_name'] = "nur für eingeloggte Nutzer sichtbar?";
                        $entry['old_value'] = $this->booleanIntoText($entry['old_value']);
                        $entry['new_value'] = $this->booleanIntoText($entry['new_value']);
                        break;
                    case "question":
                        $entry['changed_column_name'] = "Frage";
                        break;
                    case "field_type":
                        $entry['changed_column_name'] = "Fragetyp";
                        $entry['old_value'] = QuestionType::asText($entry['old_value']);
                        $entry['new_value'] = QuestionType::asText($entry['new_value']);
                        break;
                    case "is_required":
                        $entry['changed_column_name'] = "Pflichtfrage?";
                        $entry['old_value'] = $this->booleanIntoText($entry['old_value']);
                        $entry['new_value'] = $this->booleanIntoText($entry['new_value']);
                        break;
                }
            }
            unset($entry);
        }
        unset($revision);

        //check if the role of the user allows edit/delete for all answers
        $userCanEditDueToRole = $user==null? false :Auth::user()->can('update', $survey);

        //evaluation part that shows below the survey, a statistic of answers of the users who already took part in the survey

        //maybe sort questions by order here
        $evaluation = [];
        foreach ($questions as $order => $question) {

            switch ($question->field_type) {
                case QuestionType::Text:
                    $evaluation[$order] = [];
                    break; //nothing to do here except pushing an element to the array that stands for the question
                case QuestionType::Checkbox:
                    $evaluation[$order] = [
                        'Ja' => 0,
                        'Nein' => 0
                    ];
                    if ($question->is_required == false) {
                        $evaluation[$order]['keine Angabe'] = 0;
                    };
                    //checkbox options with yes,no and no entry
                    foreach ($question->getAnswerCells as $answerCell) {
                        if ($answerCell->answer === 'Ja') {
                            $evaluation[$order]['Ja'] += 1;
                        } else {
                            if ($answerCell->answer === 'Nein') {
                                $evaluation[$order]['Nein'] += 1;
                            } else {
                                if ($answerCell->answer === 'keine Angabe' and $question->is_required == false) {
                                    $evaluation[$order]['keine Angabe'] += 1;
                                }
                            }
                        }
                    }
                    break;
                case QuestionType::Dropdown:
                    $answer_options = $question->getAnswerOptions;
                    $answer_options = (array)$answer_options;
                    $answer_options = array_shift($answer_options);
                    if ($question->is_required == false) {
                        $prefer_not_to_say = new SurveyAnswerOption();
                        $prefer_not_to_say->answer_option = 'keine Angabe';
                        array_push($answer_options, $prefer_not_to_say);
                    }
                    foreach ($answer_options as $answer_option) {
                        $evaluation[$order][$answer_option->answer_option] = 0;
                        foreach ($question->getAnswerCells as $answerCell) {
                            if ($answer_option->answer_option === $answerCell->answer) {
                                $evaluation[$order][$answer_option->answer_option] += 1;
                            }
                        }
                    }
                    break;
            }
        }

        //ignore html tags in the description
        $survey->description = htmlspecialchars($survey->description, ENT_NOQUOTES);
        //if URL is in the description, convert it to clickable hyperlink (<a> tag)
        $survey->description = Utilities::surroundLinksWithTags($survey->description);

        //return all the gathered information to the survey view
        return view('surveyView', compact('survey', 'questions', 'questionCount', 'answers', 'clubs', 'userId',
            'userStatus', 'userCanEditDueToRole', 'evaluation', 'revisions', 'userParticipatedAlready','username','ldapid'));
    }

    /**
     * @param  int $id
     * @return mixed
     */
    public function edit($id)
    {
        //find survey
        $survey = Survey::findOrFail($id);

        $this->authorize('update', $survey);

        //find questions and answer options
        $questions = $survey->questions;
        foreach ($questions as $question) {
            $answer_options = $question->getAnswerOptions;
        }

        // prepare correct date and time format to be used in forms for deadline
        $date = substr(($survey->deadline), 0, 10);
        $time = substr(($survey->deadline), 11, 8);
        $isEdit = true;
        return view('editSurveyView', compact('survey', 'questions', 'answer_options', 'date', 'time', 'isEdit'));
    }

    /**
     * @param int $id
     * @param SurveyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update($id, SurveyRequest $request)
    {
        //find survey
        $survey = Survey::findOrFail($id);

        $this->authorize('update', $survey);
        $revision_survey = new Revision($survey);

        //edit existing survey
        $survey->title = $request->title;
        $survey->description = $request->description;

        //format deadline for database
        $survey->deadline = strftime("%Y-%m-%d %H:%M:%S", strtotime($request->deadlineDate . $request->deadlineTime));
        $survey->is_anonymous = isset($request->is_anonymous) ? "1" : "0";
        $survey->is_private = isset($request->is_private) ? "1" : "0";
        $survey->show_results_after_voting = isset($request->show_results_after_voting) ? "1" : "0";

        //delete password if user changes both to delete
        $deletePassword = $request->password == "delete" && $request->password_confirmation == "delete";
        $requestContainsPassword = !empty($request->password)
            && !empty($request->password_confirmation)
            && $request->password == $request->password_confirmation;
        if ($deletePassword) {
            $survey->password = '';
        } else {
            if ($requestContainsPassword) {
                $survey->password = Hash::make($request->password);
            }
        }

        //save the updates
        $survey->save();
        $revision_survey->save($survey, "Umfrage geändert");

        //get questions and answer options as arrays from the input
        $newQuestions = collect($request->questionText);
        $newAnswerOptions = $request->answerOption;

        $required = $newQuestions->map(function ($question, $index) use ($request) {
            if (isset($request->required) && isset($request->required[$index])) {
                return $request->required[$index] ? 1 : 0;
            }
            return 0;
        });


        /* get question type as array
         * 1: text field
         * 2: checkbox
         * 3: dropdown, has answer options!
         */
        $types = collect($request->type_select)->map(function ($asString) {
            return intval($asString);
        });

        $oldQuestions = $survey->questions;
        $oldAnswerOptions = $oldQuestions->map(function ($question) {
            return $question->options;
        });

        $oldQuestionTexts = $oldQuestions->pluck('question');

        $newlyCreatedQuestions = $newQuestions->diffKeys($oldQuestionTexts);
        $deletedQuestions = $oldQuestionTexts->diffKeys($newQuestions);
        $nonModifiedQuestions = $newQuestions->filter(function ($text, $order) use (
            $oldQuestions,
            $types,
            $required,
            $newAnswerOptions,
            $oldAnswerOptions
        ) {
            if ($order >= $oldQuestions->count()) {
                return false;
            }
            $isSameText = $oldQuestions->get($order)->question === $text;
            if (!$isSameText) {
                return false;
            }
            $isSameType = $oldQuestions->get($order)->field_type === $types[$order];
            if (!$isSameType) {
                return false;
            }
            $isSameRequired = $oldQuestions->get($order)->is_required === $required[$order];
            if (!$isSameRequired) {
                return false;
            }

            if ($types[$order] !== 3) {
                return true;
            }
            $oldOptions = collect($oldAnswerOptions->get($order));
            $newOptions = collect($newAnswerOptions[$order]);

            $noCreatedOptions = $newOptions->intersect($oldOptions)->count() === $newOptions->count();
            $noDeletedOptions = $oldOptions->intersect($newOptions)->count() === $oldOptions->count();
            $noModifiedOptions = $newOptions->diff($oldOptions)->count() === $newOptions->count();

            $areOptionsUnchanged = $noDeletedOptions && $noCreatedOptions && $noModifiedOptions;

            return $areOptionsUnchanged;
        });
        $modifiedQuestions = $oldQuestionTexts->diff($nonModifiedQuestions)->diffKeys($deletedQuestions);

        foreach ($newlyCreatedQuestions as $index => $question) {
            SurveyQuestion::make($survey, $index, $question, $types[$index], $required[$index],
                $newAnswerOptions[$index]);
        }

        foreach ($deletedQuestions as $index => $question) {
            $trashedQuestion = $oldQuestions->get($index);
            $trashedQuestion->options->each(function($option) { Revision::deleteWithRevision($option);});
            $trashedQuestion->cells->each(function($cell) { Revision::deleteWithRevision($cell);});
            Revision::deleteWithRevision($trashedQuestion);
        }

        foreach ($modifiedQuestions as $index => $question) {
            $questionToModify = $oldQuestions->get($index);
            $questionRevision = new Revision($questionToModify);

            $questionToModify->order = $index;
            $questionToModify->field_type = $types[$index];
            $questionToModify->is_required = $required[$index];
            $questionToModify->question = $newQuestions[$index];

            if ($types[$index] === 3) {
                $oldOptions = collect($oldAnswerOptions->get($index));
                $newOptions = collect($newAnswerOptions[$index]);

                $optionTexts = $oldOptions->map(function ($option) {
                    return $option->answer_option;
                });
                $deletedOptions = $optionTexts->diffKeys($newOptions);
                $newlyCreatedOptions = $newOptions->diffKeys($optionTexts);
                $unmodifiedOptions = $optionTexts->filter(function ($option, $optionIndex) use ($newOptions) {
                    return $newOptions[$optionIndex] === $option;
                });
                $modifiedOptions = $optionTexts->diff($unmodifiedOptions);

                foreach ($deletedOptions as $optionIndex => $option) {
                    Revision::deleteWithRevision($oldAnswerOptions->get(index)->get($optionIndex));
                }
                foreach ($newlyCreatedOptions as $optionIndex => $option) {
                    SurveyAnswerOption::make($questionToModify, $option);
                }
                foreach ($modifiedOptions as $optionIndex => $option) {
                    $oldOption = $oldAnswerOptions->get($index)->get($optionIndex);

                    $optionRevision = new Revision($oldOption);
                    $oldOption->answer_option = $newOptions[$optionIndex];

                    $oldOption->save();
                    $optionRevision->save($oldOption);
                }
            }

            $questionToModify->save();
            $questionRevision->save($questionToModify);
        }

        return redirect()->action([SurveyController::class, 'show'], [ $survey->id ]);
    }


    /*
     *  -------------------- END OF REST-CONTROLLER --------------------
     */

    /**
     * changes 0 to false and 1 to true
     * @param $boolean
     * @return null|string
     */
    private function booleanIntoText($boolean)
    {
        switch ($boolean) {
            case null:
                return null;
            case 0:
                return "Nein";
            case 1:
                return "Ja";
        }
        return "Fehler";
    }

}
