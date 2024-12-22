<?php

namespace Lara\Http\Requests;

use Lara\Http\Requests\Request;
use Carbon\Carbon;

class SurveyRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $today_date = \Carbon\Carbon::now()->toDateString();
        $rules = [
            'title' => 'string|required|max:255',
            'description' => 'string|max:1500',
            'deadlineDate' => "required|date_format:Y-m-d|after:.$today_date.",
            'deadlineTime' => "required|date_format:H:i:s",
            'password' => 'string|confirmed',

            'questionText' => 'array|required',
            'questionText.*' => 'string|required|min:1',

            'answerOption' => 'array',
            'answerOption.*.*' => 'string|min:1',

            'type_select' => 'array|required',
            'type_select.*' => 'in:1,2,3|required',
        ];

        


        $questions_type = request('type_select');
        foreach($questions_type as $key => $question_type) {
            if ($question_type === 3) {
                $rules['answerOption.' . $key] = 'required';
            }
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        foreach(request('questionText') as $key => $val) {
            $messages['questionText.'.$key.'.string'] = 'Die Frage #'.($key+1).'muss eine Zeichenkette sein.';
            $messages['questionText.'.$key.'.min'] = 'Die Frage #'.($key+1).' muss mindestens :min Zeichen enthalten.';
            $messages['questionText.'.$key.'.required'] = 'Die Frage #'.($key+1).' wurde leer gelassen. Bitte fülle sie aus oder lösche sie!';
        }

        foreach(request('type_select') as $key => $val) {
            $messages['type_select.'.$key.'.in'] = 'Für die Frage #'.($key+1).' wurde ein fehlerhafter Fragetyp ausgewählt!';
            $messages['type_select.'.$key.'.required'] = 'Für die Frage #'.($key+1).' wurde kein Fragetyp ausgewählt!';
        }

        return $messages;
    }
}
