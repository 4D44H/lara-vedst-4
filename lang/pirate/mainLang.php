<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Language Lines
    |--------------------------------------------------------------------------
    |
    | Main language lines sorted by files
    | Lines are used multiple times, when they are similar in different views.
    | That means that new lines in this file only appear, when they appeared
    | for the first time while locating the files.
    |
    | -Structure,etc. can be changed, but remember to change all trans() and strings accordingly
    |
    | You want to add a new language? Steps:
    | in config/language.php add it in the following form: 'de' => 'Deutsch',
    | in lara//lang add a new folder + file in this style
    |
    |
    */

    // /app
    // /resources/views
    // /resources/views/errors

    //-----------------------------------------------------------------------------------------------------

    // /resources/views/layouts/master.blade.php
    'notWorkingMail'        => 'Eine Planke ist lose? Versende uns eine Taube!',
    'moreInfosProjectsite'  => 'Land in Sicht? Kletter\' in den Mast!',
    'backToTop'             => 'Ins Krähennest klettern!',
    'language'              => 'Sprache',

    //-----------------------------------------------------------------------------------------------------

    // resources/views
    // resources/views/clubEventView.blade.php

    //Event types ------------------------------------
    'type'                  => 'Fall',
    'normalProgramm'        => 'Seemanslieder singen',
    'information'           => 'Ausschau halten',
    'special'               => 'Festmahl',
    'LiveBandDJ'            => 'Seeschlacht',
    'internalEvent'         => 'Feirn im engsten Kreise',
    'utilization'           => 'Passagierbeförderung',
    'outsideEvent'          => 'Treffen weit vom Hafen',
    'buffet'                => 'Mahlzeit',
    'flooding'              => 'Deck schrubben',
    'flyersPlacard'         => 'Rum verteilen',
    'marketingFlyersPlacard'=> 'Anhoyern', //used in legend.blade.php
    'preSale'               => 'Seelenverkäufer',
    'others'                => 'Sonstiges',
    'faceDone'              => 'Schon im Gesichtsbuch',
    'eventUrl'              => 'Gesichtsbuch - Kajüte',
    'priceTickets'          => 'Vorausbuchung',
    'price'                 => 'Beförderungsentgelt',
    'studentExtern'         => 'Seeleute / Landratten',

    //----------------------------------------------

    'begin'                 => 'Beginn',
    'end'                   => 'Ende',
    'eventDefaults'         => 'Was der Kapitän gesagt hat:',

    'DV-Time'               => 'An Bord sein',
    'club'                  => 'Schiff',
    'internalEventP'        => 'Feirn im engsten Kreise', // Placeholder string
    'internEventP'          => 'Feirn im engsten Kreise', // Placeholder string for example used in monthCell.blade.php

    'willShowFor'           => 'Ahoi an',

    'changeEvent'           => 'Veranstaltung ändern',
    'deleteEvent'           => 'Veranstaltung löschen',
    'confirmDeleteEvent'    => 'Diese Veranstaltung wirklich entfernen? Diese Aktion kann nicht rückgängig gemacht werden!',

    'additionalInfo'        => 'Flaschenpost',
    'moreDetails'           => 'Ansagen vom Kapitän',
    'noShifts'              => 'Diese Veranstaltung hat keine Sklaven.',

    //Button
    'showMore'              => 'Mehr!',
    'showLess'              => 'Verschwinde!',

    'hideTimes'             => 'Gläser wegpacken',

    'addComment'            => 'Blödsinn ausrufen',  //not used Line ClubEventView ~270 Placeholder message and similar

    //List of Changes
    'listChanges'           => 'Schiffsregister',

    'work'                  => 'Dienst',
    'whatChanged'           => 'Was wurde geändert?',
    'oldEntry'              => 'Alter Eintrag',
    'newEntry'              => 'Neuer Eintrag',
    'whoBlame'              => 'Wer ist schuld?',
    'whenWasIt'             => 'Wann war das?',

    //-------------------------------------------------------------------------------------------------------

    // resources/views/createClubEventView.blade.php
    'createNewVEvent'       => 'Neue Veranstaltung erstellen',
    'createNewEvent'        => 'Neues Event erstellen',
    'template'              => 'Vorlage',
    'templateNewSaveQ'      => 'Als neue Vorlage speichern?',
    'title'                 => 'Titel',
    'subTitle'              => 'Subtitel',
    'error'                 => 'Fehler',

    'showExtern'            => 'Für Externe sichtbar machen?',

    'survey'                => 'Umfrage',

    //blockString line~168
    'showForLoggedInMember'     => 'Dieses Event wird nur für eingeloggte Mitglieder sichtbar sein!',
    'showForExternOrChangeType' => 'Um sie für Externe sichtbar zu machen oder den Typ zu ändern,',
    'askTheCLOrMM'              => 'frage die Admiralität oder den Smutje.',

    'section'               => 'Abteil',
    'showFor'               => 'Zeige für',

    'passwordEntry'         => 'Passwort zum Eintragen',
    'passwordRepeat'        => 'Passwort wiederholen',
    'passwordDeleteMessage' => 'Um das Passwort zu löschen trage in beide Felder "delete" ein (ohne Anführungszeichen).',
    'unlockDate'            => 'Abfahrtszeitpunkt',
    'availableAt'           => 'Verfügbar ab',

    'moreInfos'             => 'Weitere Details',
    'public'                => 'öffentlich',
    'details'               => 'Interne Information',
    'showOnlyIntern'        => 'nur intern sichtbar',

    'backWithoutChange'     => 'Ohne Änderung zurück',

    //---------------------------------------------------------------------------------------------------------

    // resources/views/editClubEventView.blade.php
    'changeEventJob'        => 'Veranstaltung/Aufgabe ändern',
    'canceled'              => 'keelhauled',

    //Lines for editing only with permission
    'noNotThisWay'          => 'Ne, das geht so nicht...',
    'onlyThe'               => 'Nur die',
    'only'                  => 'Nur',
    'clubManagement'        => 'Admiral',
    'orThe'                 => 'oder die',
    'marketingManager'      => 'Smutje',
    'canChangeEventJob'     => 'dürfen diese Veranstaltung/Aufgabe ändern.',
    'commaThe'              => ', die ', //line number ~332

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/createSurveyView.blade.php
    'createNewSurvey'       => 'Neue Umfrage erstellen',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/editSurveyView.blade.php
    'editSurvey'            => 'Umfrage editieren',
    'confirmDeleteSurvey'   => 'Möchtest du die Umfrage ":title" wirklich löschen?',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/surveyView.blade.php
    'description'           => 'Beschreibung',
    'surveyDeadlineTo'      => 'Die Umfrage läuft noch bis',
    'at'                    => 'um',

    //result messages; can be changed with pluralization
    'noPersonAnswered'      => 'Es hat noch keine Person abgestimmt.',
    'onePersonAnswered'     => 'Es hat bereits eine Person abgestimmt.',
    'fewPersonAnswered1'    => 'Es haben bereits',
    'fewPersonAnswered2'    => 'Personen abgestimmt.',

    //tableau (head)
    'name'                  => 'Name',
    'myClub'                => 'Schiff',

    'addMe'                 => 'Anhoyern!',

    //Answers
    'yes'                   => 'Aaay!',
    'no'                    => 'Neey!',
    'noInformation'         => 'mmmh...',

    'noClub'                => 'Extern',

    'confirmDeleteAnswer'   => 'Möchtest Du diese Antwort wirklich löschen?',

    //evaluation; can be changed with pluralization
    'evaluation'            => 'Auswertung',
    'personDidNotAnswer'    => 'Person wollte keine Angaben machen.',
    'personsDidNotAnswer'   => 'Personen wollten keine Angaben machen.',
    'personAnswered'        => 'Person stimmte für',
    'personsAnswered'       => 'Personen stimmten für',

    //List of Changes
    'who'                   => 'Wer',
    'summary'               => 'Zusammenfassung',
    'affectedColumn'        => 'Betroffene Spalte',
    'oldValue'              => 'Alter Wert',
    'newValue'              => 'Neuer Wert',
    'when'                  => 'Wann',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/monthView.blade.php
    //short weekdays + CW
    'Cw' => 'KW',
    'Mo' => 'Manendach',
    'Tu' => 'Dinxendach',
    'We' => 'Woensdach',
    'Th' => 'Donresdach',
    'Fr' => 'Vridach',
    'Sa' => 'Saterdagh',
    'Su' => 'Sonnendach',


    // resources/view/partials/calendarLinkEvent.blade.php
    'addToCalendar' => 'Schiffroute auf dem Rücken tätowieren',

    // resources/views/partials/month/day.blade.php
    'createEventOnThisDate' => 'Neue Fahrt an diesem Tag ansagen',

    // resources/views/partials/month/monthCell.blade.php
    'showDetails' => 'Die Mannschaft besuchen',

    // resources/views/monthView.blade.php
    'showWeek' => 'Detaillierte Ansicht dieser Woche anzeigen',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/createSurveyView.blade.php
    'noEventsThisWeek'  => 'Keine Veranstaltungen diese Woche',
    'noSurveysThisWeek' => 'Keine Umfragen diese Woche',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/listView.blade.php
    'for'               => 'Für',
    'noEventsPlanned'   => 'sind keine Veranstaltungen geplant',
    'noEventsOn'        => 'Keine Veranstaltungen am',
    'EventsFor'         => 'Veranstaltungen für',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/log.blade.php
    // not translated - international view

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials
    // /resources/views/partials/clubEventByIdSmall.blade.php
    'noResults'             => 'Keine Treffer',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/editSchedule.blade.php
    'adjustRoster'          => 'Dienstplan anpassen',
    'serviceTypeEnter'      => 'Diensttyp hier eingeben',
    'weight'                => 'Gewicht (für Statistik)',
    'statisticalEvaluation' => 'Kombüse',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/filter.blade.php
    'allSections'           => 'Alle Schiffe',
    'comments'              => 'Ausrufe',
    'chooseAtLeastOne'      => 'Wähle mind. eine...',
    'selectAll'             => 'Alle',
    'selectNone'            => 'Keine',
    'countSectionsSelected' => ':sel von :total Schiffen',
    'noSectionSelected'     => 'Keine Sektion ausgewählt!',
    'enableAll'             => 'Alle an',
    'disableAll'            => 'Alle aus',
    'enableToDisplay'       => 'Switch on to display',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/legend.blade.php
    //handled in the event type part in the /resources/views/clubEventView.blade.php part

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/monthCell.blade.php
    'internalSurvey'        => 'Interne Umfrage',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/navigation.blade.php
    'today'                 => 'Tag',
    'month'                 => 'Jahreszwölftel',
    'week'                  => 'Trinkzeit',
    'toggleTheme'           => 'Mach das Licht aus!',
    
    //not translated the term 'logs'
    'manageClub'            => 'Schiffe verwalten',
    'manageShiftType'       => 'Diensttypen verwalten',
    'manageSections'        => 'Mannschaften verwalten',
    // TODO use Job for Service - german: Dienst maybe change to Shift - Schicht

    'manageTemplates'        => 'Vorlagen verwalten',

    //create button text
    'createNewEvent'           => 'Neue Fahrt ansagen',
    'createNewSurvey'          => 'Die Mannschaft fragen',

    //Member types
    'candidate'             => 'Frischling',
    'veteran'               => 'Seebär',
    'ex-member'             => 'Einbeiniger',
    'ex-candidate'          => 'Deserteur',
    'active'                => 'Matrose',
    'external'              => 'Landratte',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/scheduleEntryName.blade.php
    'IDoIt'                 => 'Anhoyern!', //Ich mach's!

    // /resources/views/partials/scheduleEntryStatus.blade.php
    'jobFree'               => 'Dienst frei',

    // /resources/views/partials/statisticsLeaderboards.blade.php
    'totalShifts'           => 'Flaschen',
    'leaderBoards'          => 'Bestenliste',
    'allClubs'              => 'Alle',

    // /resources/view/partials/statistics/amountOfShiftsDisplay.blade.php
    'shiftsInOtherSection'      => 'Dienste auf anderen Schiffen',
    'shiftsInOwnSection'        => 'Dienste auf eigenem Schiff',
    'floodShifts'               => 'Anzahl geleisteter Deckschrubben',

    // /resources/views/partials/clubStatistics.blade.php
    'infoFor'               => 'Thekenrechnung',

    // /resources/views/partials/personalStatistics.blade.php
    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/statisticsView.blade.php
    'monthStatistic'       => 'Monatsabrechnung',
    'yearStatistic'       => 'Jahresabrechnung',

    // /resources/views/partials/surveyAnswerStatus.blade.php
    //no new strings

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/surveyForm.blade.php
    'showOnlyForLoggedInMember' => 'nur für Crewmitglieder sichtbar',
    'showResultsOnlyForCreator' => 'Ergebnisse sind nur für den Umfragenersteller sichtbar',
    'showResultsAfterFillOut'   => 'Ergebnisse sind erst nach dem Ausfüllen sichtbar',

    'passwordSetOptional'       => 'Das Setzen eines Passworts ist optional',

    //Answer and Question options
    'answerOption'              => 'Antwortmöglichkeit',
    'question'                  => 'Frage',

    //Questionoptions
    'freeText'                  => 'Freitext',
    'checkbox'                  => 'Checkbox',
    'dropdown'                  => 'Dropdown',

    'required'                  => 'erforderlich',
    'addAnswerOption'           => 'Antwortmöglichkeit hinzufügen',
    'addQuestion'               => 'Frage hinzufügen',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellFull.blade.php
    'hide' => 'Ausblenden',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellHidden.blade.php
    'moreDetailsAfterLogInMessage'      => 'Darüber reden wir erst wenn du an Bord gehst!',
    // 'moreDetailsAfterLogInMessage2'  => 'nach dem Einloggen zugänglich.',
    // Merged with line above but there is now way to break the line (format is still ok)
    // ToDo find a solution

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellProtected.blade.php
    //no new strings

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellSurvey.blade.php
    //no new strings

    //-----------------------------------------------------------------------------------------------------------

    // /public/js ToDo add these strings
    // Maybe To do translate Javascript, tricky, " " can throw error messages
    // /public/js/vedst-script.js
    'errorMessageForgotFilter'              => 'Den Filter vergessen! Bitte setze mindestens eine Sektion, der diese Veranstaltung/Aufgabe gezeigt werden soll.',
    'errorMessageEnterPasswordForShiftPlan' => 'Bitte noch das Passwort für diesen Dienstplan eingeben:',
    'shiftTime'                             => 'Deckzeit',
    //'hideTimes' already exists

    'weekStart'                             => 'Manendach - Sonnendach',
    'hideTakenShifts'                       => 'Nur FREIe Dienste',

    //-----------------------------------------------------------------------------------------------------------

    //Controller  ToDo add these strings
    //ShiftController
    //action description
    'addedComment'              => 'Kommentar hinzugefügt',
    'deletedComment'            => 'Kommentar gelöscht',
    'changedComment'            => 'Kommentar geändert',

    'errorMessageAbortDeletion' => 'Fehler: Löschvorgang abgebrochen - der Dienstplaneintrag existiert nicht.',

    //SurveyController
    'messageSurveyDeleted'      => 'Umfrage gelöscht!',

    //SurveyAnswerController
    'messageSuccessfulVoted'    => 'Erfolgreich abgestimmt!',
    'messageSuccessfulDeleted'  => 'Erfolgreich gelöscht!',

    //-----------------------------------------------------------------------------------------------------------

    //placeholder strings (e.g. used in buttons or text fields)

    /*
    | instead of { trans('message.key' }} use  Lang::get('message.key') in the view
    */

    //ClubEvent
    'cantDeleteEvent'               => 'Nur Administratoren, CLs und Marketing-Mitglieder, die der Sektion der Veranstaltung angehören, können die Veranstaltung löschen.',
    'cantUpdateEvent'               => 'Nur Administratoren, CLs und Marketing-Mitglieder, die der Sektion der Veranstaltung angehören, und der Ersteller der Veranstaltung können die Veranstaltung ändern.',
    'addCommentHere'                => 'Blödsinn ausrufen',
    'enterPasswordHere'             => 'Passwort hier eingeben',
    'placeholderTitleWineEvening'   => 'z.B. Weinabend',
    //'placeholderTitleWineEvening'

    'placeholderSubTitleWineEvening'=> 'z.B. Das Leben ist zu kurz, um schlechten Grogg zu trinken',
    //'placeholderSubTitleWineEvening'

    'placeholderPublicInfo'         => 'z.B. Karten nur im Vorverkauf',
    'placeholderPrivateDetails'     => 'z.B. DJ-Tisch wird gebraucht',

    //Survey
    'addAnswerHere'                 => 'Antwort hier hinzufügen',
    'createSurvey'                  => 'Umfrage erstellen', //Button text

    //Partials
    //Navigation
    'clubNumber'                    => 'Matrose',
    'password'                      => 'Das geheime Wort',
    'logIn'                         => 'An Bord gehen',
    'logOut'                        => 'Über Bord gehen',
    'light'                         => 'Tagschicht',
    'dark'                          => 'Nachtfahrt',
    //ShiftName
    '=FREI='                        => '=FREI=', //not used yet

    //ShiftTitle
    'optional'                      => 'Optional',
    'optionalShort'                 => 'Opt.',

    //surveyForm
    'placeholderSurveyTitle'        => 'Titel:',
    'placeholderTitleSurvey'        => 'z.B. Teilnahme an der Clubfahrt',
    'placeholderDescription'        => 'Beschreibung:',
    'placeholderActiveUntil'        => 'Aktiv bis:',

    // Misc.
    'guest'                         => 'Passagier',
    'accessDenied'                  => 'Moment mal... Nur eingeloggte Crewmitglieder mit ausreichendem Rang dürfen hier rein! Logge dich ein, oder komm zur nächsten Versammlung und heuer an.',
    'filter'                        => 'Filtern',
    'submit'                        => 'Absenden',


    ////////////////
    // Management //
    ////////////////

    'management'                    => 'Verwaltung',
    'shiftType'                       => 'Diensttyp',
    'shiftTypes'                      => 'Diensttypen',
    'replaceAll'                    => 'alle ersetzen',
    'shift'                         => 'Dienst',
    'start'                         => 'Beginn',
    'end'                           => 'Ende',
    'weight'                        => 'Wert',
    'actions'                       => 'Aktionen',
    'deleteConfirmation'            => 'Möchtest du folgenden Diensttyp wirklich löschen:',
    'warningNotReversible'          => 'Diese Aktion kann man nicht rückgängig machen!',
    'editDetails'                   => 'Details anpassen',
    'deleteThisShiftType'             => 'Entfernen',
    'editShiftType'                 => 'Diensttyp bearbeiten',

    'reset'                         => 'Zurücksetzen',
    'update'                        => 'Änderungen speichern',
    'delete'                        => 'löschen',
    'shiftTypeNeverUsed'              => 'Dieser Diensttyp wird bei keinem einzigen Event benutzt... Traurig, so was... Vielleicht wäre es sinnvoll, ihn einfach zu',
    'shiftTypeUsedInFollowingEvents'  => 'Dieser Dienstyp wird bei folgenden Events eingesetzt. Um ihn zu entfernen, ersetze jede Instanz erst mit einem anderen Diensttyp.',
    'event'                         => 'Event',
    'date'                          => 'Datum',
    'substituteThisInstance'        => 'Ersetzen durch...',

    'cantTouchThis'                 => 'Netter Versuch - du darfst das nicht einfach ändern! Frage die Clubleitung oder Markleting ;)',
    'cantBeBlank'                   => 'Diese Werte dürfen nicht leer sein.',
    'nonNumericStats'               => 'Statistische Wertung muss man mit Ziffern eingeben ;)',
    'negativeStats'                 => 'Statistische Wertung darf nicht negativ sein.',
    'changesSaved'                  => 'Änderungen erfolgreich gespeichert.',
    'deleteFailedShiftTypeInUse'      => 'Diensttyp wurde NICHT gelöscht, weil er noch im Einsatz ist. Hier kannst du es ändern.',
    'adminsOnly'                    => 'Sorry, aber das darf nur der Kapitän!',
    ////////////////////////
    // Section management //
    ////////////////////////
    'color'                         => 'Farbe',
    'sectionExists'                 => 'Es it bereits ein Schiff mit diesem Namen vom Stapel gelaufen!',
    'newSection'                    => 'Neues Schiff',
    'createSection'                 => 'Neues Schiff bauen',
    'privateClubName'               => 'Soll mein Piratenname bekannt sein?',
    'privateClubNameYes'            => 'Nein, zeig\'s keiner Landratte!',
    'privateClubNameNo'             => 'Klar, jeder soll mich kennen!',
    'privateClubNameNull'           => 'Ich lass den Kapitän entscheiden!',
    'upcomingShifts'                => 'Wofür ich zunächst nüchtern bleiben soll',

    //////////
    // ICal //
    //////////

    'icalfeeds'                     =>  'Kalenderfeed im iCal-Format',
    'publishEvent'                  =>  'Event veröffentlichen',
    'unpublishEvent'                =>  'War alles nur Seemansgarn',
    'createAndPublish'              =>  'Anker los und Angreifen',
    'createUnpublished'             =>  'Leinen los und Schleichfahrt',
    'eventIsPublished'              =>  'Angriff - über diese Fahrt werden Lieder bereits gesungen',
    'eventIsUnpublished'            =>  'Schleichfahrt - dieses Abenteuer wird von Barden nicht erwähnt',
    'confirmPublishingEvent'        =>  'Möchtest du dieses Event wirklich zum Kalenderfeed hinzufügen?',
    'confirmUnpublishingEvent'      =>  'Möchtest du dieses Event aus dem Kalenderfeed wirklich entfernen?',
    'iCal'                          =>  'iCal',

    /////////////////////////
    // Template management //
    /////////////////////////

    'Search'                        => 'Suche',
    'facebookNeeded'                => 'Piratenbuch benötigt',
    'createTemplate'                => 'Kaperplan erstellen',

    /////////////////////////
    // User management     //
    /////////////////////////
    'status'                        => 'Status',
    'editUser'                      => 'Benutzer bearbeiten',
    'availableRoles'                => 'Verfügbare Rollen',
    'activeRoles'                   => 'Aktive Rollen',
    'changesWereReset'              => 'Deine Änderungen wurden zurückgesetzt. Vor dem Absenden solltest du diese nochmals anwenden.',
    'roleManagement'                => 'Berechtigungen verwalten',
    'changesSaved'                  => 'Eingaben wurden gespeichert.',
    'sectionChanged'                => 'Die Sektion des Benutzers hat sich geändert. Denke daran, seine Berechtigungen ebenfalls anzupassen.',

    /////////////////
    // Legal stuff //
    /////////////////

    'privacyPolicy'     => 'Datenschutz',
    'impressum'         => 'Impressum',

    'agreeWithPrivacy'  => 'Du musst unserer Datenschutzerklärung zustimmen, um Lara zu nutzen. Eine Zusammenfassung findest du in den Tabs. Bitte stimme unten der Nutzung deiner Daten zu.',
    'privacyAccepted'   => 'Danke, jetzt kannst du Lara weiter benutzen',
    'fatalErrorUponSaving' => 'Etwas ist schief gelaufen. Versuche es später erneut oder wende dich an einen Administrator, um Unterstützung zu erhalten.',
    'privacyAgree'      => 'Ich stimme zu',
    'waitOneSecond'     => 'Nur eine Sekunde...',

    ///////////////////
    // Personal Page //
    ///////////////////
    'userPersonalPage' => 'Persönliche Kajüte',
    'settings'         => 'Einstellungen',

    ///////////////////
    // 2fa           //
    ///////////////////
    '2fa'             => 'Aktuelle Losung',
    '2fa.setup'       => 'Richten Sie Ihre Zwei-Faktor-Authentifizierung ein, indem Sie den folgenden QR-Code scannen. Alternativ können Sie den Code verwenden',
    '2fa.verifyWorking' => 'Um Sicherzugehen, dass die Losung übergeben ist geben Sie bitte den aktuellen Code ein.',
    '2fa.unregister'       => 'Zwei-Faktor-Authentisierung entfernen',
];
