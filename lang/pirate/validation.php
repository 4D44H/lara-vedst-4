<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute muss akzeptiert werden.',
    'active_url'           => ':attribute ist keine gültige oder aktive URL.',
    'after'                => ':attribute muss nach dem :date sein.',
    'alpha'                => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => ':attribute darf nur alpha-numerische Zeichen enthalten.',
    'alpha_num'            => ':attribute darf nur Buchstaben und Zahlen enthalten.',
    'array'                => ':attribute muss vom Typ Array sein.',
    'before'               => ':attribute muss vor dem :date sein.',
    'between'              => [
        'numeric' => 'Der Wert :attribute muss zwischen :min und :max liegen.',
        'file'    => 'Die Datei :attribute muss zwischen :min und :max Kilobyte groß sein.',
        'string'  => ':attribute muss minimal :min und maximal :max Zeichen enthalten.',
        'array'   => 'Das Array :attribute muss zwischen :min und :max Elemente enthalten.',
    ],
    'boolean'              => ':attribute muss entweder wahr oder falsch sein.',
    'confirmed'            => ':attribute stimmt nicht mit der Bestätigung überein.',
    'date'                 => ':attribute ist kein gültiges Datum.',
    'date_format'          => 'Das Datum :attribute entspricht nicht dem geforderten Format (:format).',
    'different'            => ':attribute und :other müssen sich unterscheiden.',
    'digits'               => ':attribute muss eine Zahl mit :digits Ziffern sein.',
    'digits_between'       => ':attribute muss eine Zahl mit minimal :min und maxmimal :max Ziffern sein.',
    'distinct'             => ':attribute enthält einen Wert, der doppelt darin vorkommt.',
    'email'                => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'exists'               => ':attribute ist üngültig, da es nicht in der Datenbank vorkommt.',
    'filled'               => ':attribute wird benötigt.',
    'image'                => ':attribute muss ein Bild sein.',
    'in'                   => ':attribute ist üngültig.',
    'in_array'             => ':attribute existiert nicht in :other.',
    'integer'              => ':attribute muss eine Zahl (Integer) sein.',
    'ip'                   => ':attribute muss eine gültige IP-Adresse sein.',
    'json'                 => ':attribute muss eine gültige JSON-Zeichenkette sein.',
    'max'                  => [
        'numeric' => ':attribute muss kleiner als :max sein.',
        'file'    => ':attribute muss kleiner als :max Kilobyte sein.',
        'string'  => ':attribute muss kürzer als :max Zeichen sein.',
        'array'   => ':attribute muss weniger als :max Elemente enthalten.',
    ],
    'mimes'                => ':attribute muss eine Datei vom Dateityp :values sein.',
    'min'                  => [
        'numeric' => ':attribute muss mindestens :min groß sein.',
        'file'    => ':attribute muss mindestens :min Kilobyte groß sein.',
        'string'  => ':attribute muss mindestens :min Zeichen lang sein.',
        'array'   => ':attribute muss mindestens :min Elemente enthalten.',
    ],
    'not_in'               => ':attribute ist üngültig.',
    'numeric'              => ':attribute muss eine Zahl sein.',
    'present'              => ':attribute field muss existieren.',
    'regex'                => 'Das Format von :attribute ist ungültig.',
    'required'             => ':attribute wird benötigt.',
    'required_if'          => ':attribute wird benötigt, wenn :other gleich :value ist.',
    'required_unless'      => ':attribute wird benötigt, außer :other ist in :values vorhanden.',
    'required_with'        => ':attribute wird benötigt, wenn :values existiert.',
    'required_with_all'    => ':attribute wird benötigt, wenn :values existiert.',
    'required_without'     => ':attribute wird benötigt, wenn :values nicht existiert.',
    'required_without_all' => ':attribute wird benötigt, wenn keiner der folgenden Werte existiert: :values.',
    'same'                 => ':attribute and :other must match.',
    'size'                 => [
        'numeric' => ':attribute muss gleich :size sein.',
        'file'    => ':attribute muss :size Kilobyte groß sein.',
        'string'  => ':attribute muss :size Zeichen enthalten.',
        'array'   => ':attribute muss :size Elemnte enthalten.',
    ],
    'string'               => ':attribute muss eine Zeichenkette sein.',
    'timezone'             => ':attribute muss eine gültige Zeitzone sein.',
    'unique'               => ':attribute wird schon verwendet.',
    'url'                  => 'Das Format von :attribute ist nicht gültig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'title' => [
            'required' => 'Der Titel darf nicht leer bleiben!',
        ],
        'deadlineDate' =>[
            'required' => 'Für die Anzeige im Kalender darf das Datum der Deadline nicht leer bleiben.',
            'date_format' => 'Bitte gib das Datum der Deadline in dieser Form an: YYYY-MM-DD.',
            'after' => 'Die Deadline muss in der Zukunft liegen.'
        ],
        'deadlineTime' =>[
            'required' => 'Für die Anzeige im Kalender darf die Zeit der Deadline nicht leer bleiben.',
            'date_format' => 'Bitte gib die Zeit der Deadline in dieser Form an: hh:mm:ss.',
        ],
        'password' => [
            'confirmed' => 'Die eingegebenen Passwörter stimmen nicht überein!',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
