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
    'notWorkingMail'        => 'Etwas funktioniert nicht? Schreibe uns eine Mail!',
    'moreInfosProjectsite'  => 'Mehr Infos? Besuche uns auf GitHub!',
    'backToTop'             => 'Zurück zum Seitenanfang',
    'language'              => 'Sprache',

    //-----------------------------------------------------------------------------------------------------

    // resources/views
    // resources/views/clubEventView.blade.php

    //Event types ------------------------------------
    'type'                  => 'Typ',
    'normalProgramm'        => 'normales Programm',
    'information'           => 'Information',
    'special'               => 'Spezial',
    'LiveBandDJ'            => 'Live Band / Live DJ / Lesung',
    'internalEvent'         => 'interne Veranstaltung',
    'utilization'           => 'Nutzung',
    'outsideEvent'          => 'Außenveranstaltung',
    'buffet'                => 'Buffet',
    'flooding'              => 'Fluten',
    'flyersPlacard'         => 'Flyer / Plakatieren',
    'marketingFlyersPlacard'=> 'Marketing / Flyer / Plakate', //used in legend.blade.php
    'preSale'               => 'Vorverkauf',
    'others'                => 'Sonstiges',
    'faceDone'              => 'Online Werbung erledigt',
    'eventUrl'              => 'Event - URL',
    'priceTickets'          => 'Vorverkaufspreis',
    'price'                 => 'Eintrittspreis',
    'studentExtern'         => 'Student / Vollzahler',

    //----------------------------------------------

    'begin'                 => 'Beginn',
    'end'                   => 'Ende',
    'eventDefaults'         => 'Standardwerte für Events:',

    'DV-Time'               => 'DV-Zeit',
    'club'                  => 'Verein',
    'internalEventP'        => 'Interne Veranstaltung', // Placeholder string
    'internEventP'          => 'Internes Event',        // Placeholder string for example used in monthCell.blade.php

    'willShowFor'           => 'wird angezeigt für',

    'changeEvent'           => 'Veranstaltung ändern',
    'deleteEvent'           => 'Veranstaltung löschen',
    'confirmDeleteEvent'    => 'Diese Veranstaltung wirklich entfernen? Diese Aktion kann nicht rückgängig gemacht werden!',

    'additionalInfo'        => 'Weitere Details',
    'moreDetails'           => 'Interne Information',
    'noShifts'              => 'Diese Veranstaltung hat keine Dienste.',

    //Button
    'showMore'              => 'mehr anzeigen',
    'showLess'              => 'weniger anzeigen',

    'hideTimes'             => 'Zeiten ausblenden',

    'addComment'            => 'Kommentar hier hinzufügen',  //not used Line ClubEventView ~270 Placeholder message and similar

    //List of Changes
    'listChanges'           => 'Liste der Änderungen',

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
    'askTheCLOrMM'              => 'frage die Sektionsleitung oder die Marketingverantwortlichen.',

    'section'               => 'Sektion',
    'showFor'               => 'Zeige für',

    'passwordEntry'         => 'Passwort zum Eintragen',
    'passwordRepeat'        => 'Passwort wiederholen',
    'passwordDeleteMessage' => 'Um das Passwort zu löschen trage in beide Felder "delete" ein (ohne Anführungszeichen).',
    'unlockDate'           => 'Entsperrzeitpunkt',
    'availableAt'           => 'Verfügbar ab',

    'moreInfos'             => 'Weitere Details',
    'public'                => 'öffentlich',
    'details'               => 'Interne Information',
    'showOnlyIntern'        => 'nur intern sichtbar',

    'backWithoutChange'     => 'Ohne Änderung zurück',

    //---------------------------------------------------------------------------------------------------------

    // resources/views/editClubEventView.blade.php
    'changeEventJob'        => 'Veranstaltung/Aufgabe ändern',
    'canceled'              => 'abgesagt',

    //Lines for editing only with permission
    'noNotThisWay'          => 'Ne, das geht so nicht...',
    'onlyThe'               => 'Nur die',
    'only'                  => 'Nur',
    'clubManagement'        => 'Sektionsleitung',
    'orThe'                 => 'oder die',
    'marketingManager'      => 'Marketingverantwortlichen',
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
    'myClub'                => 'Club',

    'addMe'                 => 'Mich eintragen!',

    //Answers
    'yes'                   => 'Ja',
    'no'                    => 'Nein',
    'noInformation'         => 'keine Angabe',

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
    'Mo' => 'Montag',
    'Tu' => 'Dienstag',
    'We' => 'Mittwoch',
    'Th' => 'Donnerstag',
    'Fr' => 'Freitag',
    'Sa' => 'Samstag',
    'Su' => 'Sonntag',


    // resources/view/partials/calendarLinkEvent.blade.php
    'addToCalendar' => 'Event zu bevorzugter Kalenderlösung hinzufügen',

    // resources/views/partials/month/day.blade.php
    'createEventOnThisDate' => 'Neues Event an diesem Tag erstellen',

    // resources/views/partials/month/monthCell.blade.php
    'showDetails' => 'Details anzeigen',

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
    'statisticalEvaluation' => 'Statistik',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/filter.blade.php
    'allSections'           => 'Alle Sektionen',
    'comments'              => 'Kommentare',
    'chooseAtLeastOne'      => 'Wähle mind. eine...',
    'selectAll'             => 'Alle',
    'selectNone'            => 'Keine',
    'countSectionsSelected' => ':sel von :total Sektionen',
    'noSectionSelected'     => 'Keine Sektion ausgewählt!',
    'enableAll'             => 'Alle an',
    'disableAll'            => 'Alle aus',
    'enableToDisplay'       => 'Anzeige aktivieren',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/legend.blade.php
    //handled in the event type part in the /resources/views/clubEventView.blade.php part

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/monthCell.blade.php
    'internalSurvey'        => 'Interne Umfrage',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/navigation.blade.php
    'today'                 => 'Tag',
    'month'                 => 'Monat',
    'week'                  => 'Woche',
    'toggleTheme'           => 'Thema umstellen',

    //not translated the term 'logs'
    'manageClub'            => 'Clubs verwalten',
    'manageShiftType'       => 'Diensttypen verwalten',
    'manageSections'        => 'Sektionen verwalten',
    // TODO use Job for Service - german: Dienst maybe change to Shift - Schicht

    'manageTemplates'        => 'Vorlagen verwalten',

    //create button text
    'createNewEvent'           => 'Neues Event erstellen',
    'createNewSurvey'          => 'Neue Umfrage erstellen',

    //Member types
    'candidate'             => 'Kandidat',
    'veteran'               => 'Veteran',
    'ex-member'             => 'ex-Mitglied',
    'ex-candidate'          => 'ex-Kandidat',
    'active'                => 'Aktiv',
    'external'              => 'Extern',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/scheduleEntryName.blade.php
    'IDoIt'                 => 'Ich mache es!', //Ich mach's!

    // /resources/views/partials/scheduleEntryStatus.blade.php
    'jobFree'               => 'Dienst frei',

    // /resources/views/partials/statisticsLeaderboards.blade.php
    'totalShifts'           => 'Dienste',
    'leaderBoards'          => 'Bestenliste',
    'allClubs'              => 'Alle',

    // /resources/view/partials/statistics/amountOfShiftsDisplay.blade.php
    'shiftsInOtherSection'      => 'Dienste in anderen Sektionen',
    'shiftsInOwnSection'        => 'Dienste in eigener Sektion',
    'floodShifts'               => 'Anzahl geleisteter Flutdienste',

    // /resources/views/partials/clubStatistics.blade.php
    'infoFor'               => 'Mitgliederstatistik',

    // /resources/views/statisticsView.blade.php
    'monthStatistic'       => 'Monatsstatistik',
    'yearStatistic'       => 'Jahresstatistik',

    // /resources/views/partials/personalStatistics.blade.php
    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/surveyAnswerStatus.blade.php
    //no new strings

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/surveyForm.blade.php
    'showOnlyForLoggedInMember' => 'nur für eingeloggte Nutzer sichtbar',
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
    'moreDetailsAfterLogInMessage'      => 'Weitere Details sind für Mitglieder nach dem Einloggen zugänglich.',
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
    'shiftTime'                             => 'Dienstzeiten',
    //'hideTimes' already exists

    'weekStart'                             => 'Montag - Sonntag',
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
    'addCommentHere'                => 'Kommentar hier hinzufügen',
    'enterPasswordHere'             => 'Passwort hier eingeben',
    'placeholderTitleWineEvening'   => 'z.B. Weinabend',
    //'placeholderTitleWineEvening'

    'placeholderSubTitleWineEvening'=> 'z.B. Das Leben ist zu kurz, um schlechten Wein zu trinken',
    //'placeholderSubTitleWineEvening'

    'placeholderPublicInfo'         => 'z.B. Karten nur im Vorverkauf',
    'placeholderPrivateDetails'     => 'z.B. DJ-Tisch wird gebraucht',

    //Survey
    'addAnswerHere'                 => 'Antwort hier hinzufügen',
    'createSurvey'                  => 'Umfrage erstellen', //Button text

    //Partials
    //Navigation
    'clubNumber'                    => 'Clubnummer',
    'password'                      => 'Passwort',
    'logIn'                         => 'Anmelden',
    'logOut'                        => 'Abmelden',
    'light'                         => 'Hell',
    'dark'                          => 'Dunkel',
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
    'guest'                         => 'Gast',
    'accessDenied'                  => 'Moment mal... Nur eingeloggte Mitglieder mit ausreichender Berechtigung dürfen hier rein! Logge dich ein, oder komm zur nächsten Versammlung und werde Mitglied.',
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
    'deleteThisShiftType'           => 'Entfernen',
    'editShiftType'                 => 'Diensttyp bearbeiten',

    'reset'                         => 'Zurücksetzen',
    'update'                        => 'Änderungen speichern',
    'delete'                        => 'löschen',
    'shiftTypeNeverUsed'              => 'Dieser Diensttyp wird bei keinem einzigen Event benutzt... Traurig, so was... Vielleicht wäre es sinnvoll, ihn einfach zu',
    'shiftTypeUsedInFollowingEvents'  => 'Dieser Dienstyp wird bei folgenden Events eingesetzt. Um ihn zu entfernen, ersetze jede Instanz erst mit einem anderen Diensttyp.',
    'shiftTypeUsedInFollowingTemplates'  => 'Dieser Dienstyp wird bei folgenden Vorlagen eingesetzt. Um ihn zu entfernen, ersetze jede Instanz erst mit einem anderen Diensttyp.',
    'event'                         => 'Event',
    'date'                          => 'Datum',
    'substituteThisInstance'        => 'Ersetzen durch...',

    'cantTouchThis'                 => 'Netter Versuch - du darfst das nicht einfach ändern! Frage die Sektionsleitung oder Markleting ;)',
    'cantBeBlank'                   => 'Diese Werte dürfen nicht leer sein.',
    'nonNumericStats'               => 'Statistische Wertung muss man mit Ziffern eingeben ;)',
    'negativeStats'                 => 'Statistische Wertung darf nicht negativ sein.',
    'changesSaved'                  => 'Änderungen erfolgreich gespeichert.',
    'deleteFailedShiftTypeInUse'    => 'Diensttyp wurde NICHT gelöscht, weil er noch im Einsatz ist. Hier kannst du es ändern.',
    'adminsOnly'                    => 'Sorry, aber das darf nur ein Adminstrator!',

    ////////////////////////
    // Section management //
    ////////////////////////
    'color'                         => 'Farbe',
    'sectionExists'                 => 'Es gibt bereits eine Sektion mit diesem Namen, bitte gib einen anderen ein!',
    'newSection'                    => 'Neue Sektion',
    'createSection'                 => 'Neue Sektion anlegen',
    'privateClubName'               => 'Soll mein Clubname von Gästen versteckt werden?',
    'privateClubNameYes'            => 'Ja, bitte anonymisieren',
    'privateClubNameNo'             => 'Nein, jeder darf meinen Clubnamen sehen',
    'privateClubNameNull'           => 'Sektionsvorgabe verwenden',
    'upcomingShifts'                => 'Bevorstehende Dienste',

    //////////
    // ICal //
    //////////

    'icalfeeds'                     =>  'Kalenderfeed im iCal-Format',
    'publishEvent'                  =>  'Event veröffentlichen',
    'unpublishEvent'                =>  'Event aus dem Kalenderfeed entfernen',
    'createAndPublish'              =>  'Erstellen und veröffentlichen',
    'createUnpublished'             =>  'Erstelle unveröffentlicht',
    'eventIsPublished'              =>  'Event wurde veröffentlicht und ist zum Kalenderfeed hinzugefügt',
    'eventIsUnpublished'            =>  'Event ist nicht veröffentlicht ist im Kalendarfeed nicht sichtbar',
    'confirmPublishingEvent'        =>  'Möchtest du dieses Event wirklich zum Kalenderfeed hinzufügen?',
    'confirmUnpublishingEvent'      =>  'Möchtest du dieses Event aus dem Kalenderfeed wirklich entfernen?',
    'iCal'                          =>  'iCal',

    /////////////////////////
    // Template management //
    /////////////////////////

    'search'                        => 'Suche',
    'facebookNeeded'                => 'Online bewerben',
    'createTemplate'                => 'Vorlage erstellen',

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
    'userPersonalPage' => 'Persönliche Seite',
    'settings'         => 'Einstellungen',

    ///////////////////
    // 2fa           //
    ///////////////////
    '2fa'             => 'Zwei-Faktor-Authentisierung',
    '2fa.setup'       => 'Richten Sie Ihre Zwei-Faktor-Authentifizierung ein, indem Sie den folgenden QR-Code scannen. Alternativ können Sie den Code verwenden.',
    '2fa.verifyWorking' => 'Um sicher zu gehen, dass die Einrichtung vollständig ist geben Sie bitte den aktuellen Code ein.',
    '2fa.unregister'       => 'Zwei-Faktor-Authentisierung entfernen?',
];
