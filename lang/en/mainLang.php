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
    | -Structure,etc. can be changed, but remember to change all __() and strings accordingly
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
    'notWorkingMail'        => 'Something isn\'t working? Write us an e-mail!',
    'moreInfosProjectsite'  => 'Want to know more? Visit us on GitHub!',
    'backToTop'             => 'Back to top',
    'language'              => 'Language',

    //-----------------------------------------------------------------------------------------------------

    // resources/views
    // resources/views/clubEventView.blade.php
    //Event types ------------------------------------
    'type'                      => 'Type',
    'normalProgramm'            => 'Normal Programm',
    'information'               => 'Information',
    'special'                   => 'Special',
    'LiveBandDJ'                => 'Live Band / Live DJ / Reading',
    'internalEvent'             => 'Internal event',
    'utilization'               => 'Leasing',
    'outsideEvent'              => 'Outside event',
    'buffet'                    => 'Buffet',
    'flooding'                  => 'Flooding',
    'flyersPlacard'             => 'Flyer / poster distribution',
    'marketingFlyersPlacard'    => 'Marketing / Flyer / Posters', //used in legend.blade.php
    'preSale'                   => 'Tickets presale',
    'others'                    => 'Others',
    'faceDone'                  => 'Facebook event done',
    'eventUrl'                  => 'Event link',
    'priceTickets'              => 'Presales price',
    'price'                     => 'Entrance fee',
    'studentExtern'             => 'Student / Full price',

    //----------------------------------------------

    'begin'                 => 'Start',
    'end'                   => 'End',
    'eventDefaults'         => 'Default values for events:',

    'DV-Time'               => 'Preparations',
    'club'                  => 'Club',
    'internalEventP'        => 'Internal event', // Placeholder string
    'internEventP'          => 'Internal event', //Placeholder string for example used in monthCell.blade.php

    'willShowFor'           => 'will be shown for',

    'changeEvent'           => 'Change Event',
    'deleteEvent'           => 'Delete Event',
    'confirmDeleteEvent'    => 'Are you sure you want to delete this event? This action can not be undone!',

    'additionalInfo'        => 'Additional details',
    'moreDetails'           => 'Internal information',
    'noShifts'              => 'This event has no shifts.',

    //Button
    'showMore'              => 'show more',
    'showLess'              => 'show less',

    'hideTimes'             => 'Hide times',

    'addComment'            => 'add comment here',  //not used Line ClubEventView ~270 Placeholder message and similar

    //List of Changes
    'listChanges'           => 'List of changes',

    'work'                  => 'Shift', //Dienst
    'whatChanged'           => 'What was changed?',
    'oldEntry'              => 'Old entry',
    'newEntry'              => 'New entry',
    'whoBlame'              => 'Who is to blame?',
    'whenWasIt'             => 'When was it?',

    //-------------------------------------------------------------------------------------------------------

    // resources/views/createClubEventView.blade.php
    'createNewVEvent'       => 'Create new Event', //Veranstaltung
    'createNewEvent'        => 'Create new Event', //Event
    'template'              => 'Template',
    'templateNewSaveQ'      => 'Save as new template?',
    'title'                 => 'Title',
    'subTitle'              => 'Subtitle',
    'error'                 => 'Error',

    'showExtern'            => 'Make it visible to guests?',

    'survey'                => 'Survey',

    //blockString line~168
    'showForLoggedInMember'     => 'This event will only be visible to logged in members!',
    'showForExternOrChangeType' => 'To make it visible for guests or to change the event type,',
    'askTheCLOrMM'              => 'ask the club management or marketing managers.',

    'section'               => 'Section',
    'showFor'               => 'Show to',

    'passwordEntry'         => 'Password to enter',
    'passwordRepeat'        => 'Confirm Password',
    'passwordDeleteMessage' => 'To delete the password, enter "delete" (without quotation marks) in both fields.',
    'unlockDate'           => 'Unlock Timestamp',
    'availableAt'           => 'available at',

    'moreInfos'             => 'Additional details',
    'public'                => 'public',
    'details'               => 'Internal information',
    'showOnlyIntern'        => 'only visible to logged in users',

    'backWithoutChange'     => 'Go back without saving changes',

    //---------------------------------------------------------------------------------------------------------

    // resources/views/editClubEventView.blade.php
    'changeEventJob'        => 'Change Event/Task',
    'canceled'              => 'canceled',

    //Lines for editing only with permission
    'noNotThisWay'          => 'No, not this way...',
    'onlyThe'               => 'Only the',
    'only'                  => 'Only',
    'clubManagement'        => 'club management',
    'orThe'                 => 'or',
    'marketingManager'      => 'marketing managers',
    'canChangeEventJob'     => 'may change this event/task.',
    'commaThe'              => ', the ', //line number ~332

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/createSurveyView.blade.php
    'createNewSurvey'       => 'Create new survey',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/editSurveyView.blade.php
    'editSurvey'            => 'Edit survey',
    'confirmDeleteSurvey'   => 'Are you sure you want to delete the survey ":title"?',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/surveyView.blade.php
    'description'           => 'Description',
    'surveyDeadlineTo'      => 'The Survey is open till',
    'at'                    => 'at',

    //result messages; can be changed with pluralization
    'noPersonAnswered'      => 'No person has voted yet',
    'onePersonAnswered'     => 'One person already answered',
    'fewPersonAnswered1'    => '', // empty on purpose, because - English grammar ;)
    'fewPersonAnswered2'    => 'persons have answered already.',

    //tableau (head)
    'name'                  => 'Name',
    'myClub'                => 'Club',

    'addMe'                 => 'Add Me!',

    //Answers
    'yes'                   => 'Yes',
    'no'                    => 'No',
    'noInformation'         => 'No answer given',

    'noClub'                => 'No club',

    'confirmDeleteAnswer'   => 'Do you really want to delete this answer?',

    //evaluation; can be changed with pluralization
    'evaluation'            => 'Evaluation',
    'personDidNotAnswer'    => 'Person did not answer.',
    'personsDidNotAnswer'   => 'Persons did not answer.',
    'personAnswered'        => 'Person voted for',
    'personsAnswered'       => 'Persons voted for',

    //List of Changes
    'who'                   => 'Who',
    'summary'               => 'Summary',
    'affectedColumn'        => 'Affected column',
    'oldValue'              => 'Old value',
    'newValue'              => 'New value',
    'when'                  => 'When',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/monthView.blade.php
    //short weekdays + CW
    'Cw' => 'Week',
    'Mo' => 'Monday',
    'Tu' => 'Tuesday',
    'We' => 'Wednesday',
    'Th' => 'Thursday',
    'Fr' => 'Friday',
    'Sa' => 'Saturday',
    'Su' => 'Sunday',

    // resources/view/partials/calendarLinkEvent.blade.php
    'addToCalendar' => 'Add this event to your favorite calendar solution',

    // resources/views/partials/month/day.blade.php
    'createEventOnThisDate' => 'Create new event on this day',

    // resources/views/partials/month/monthCell.blade.php
    'showDetails' => 'Show details',

    // resources/views/monthView.blade.php
    'showWeek' => 'Switch to this week\'s detailed view',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/createSurveyView.blade.php
    'noEventsThisWeek'  => 'No Events this week',
    'noSurveysThisWeek' => 'No Surveys this week',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/listView.blade.php
    'for'               => 'For',
    'noEventsPlanned'   => 'no Events are planned',
    'noEventsOn'        => 'No Events on',
    'EventsFor'         => 'Events for',

    //-----------------------------------------------------------------------------------------------------------

    // resources/views/log.blade.php
    // not translated - international view

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials
    // /resources/views/partials/clubEventByIdSmall.blade.php
    'noResults'         => 'No results',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/editSchedule.blade.php
    'adjustRoster'          => 'Adjust schedule',
    'serviceTypeEnter'      => 'Enter shift type here',
    'weight'                => 'Statistical weight',
    'statisticalEvaluation' => 'Statistics',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/filter.blade.php
    'allSections'           => 'All sections',
    'comments'              => 'Comments',
    'chooseAtLeastOne'      => 'Choose at least one...',
    'selectAll'             => 'Select all',
    'selectNone'            => 'Deselect all',
    'countSectionsSelected' => ':sel of :total sections',
    'noSectionSelected'     => 'No section selected!',
    'enableAll'             => 'Enable all',
    'disableAll'            => 'Disable all',
    'enableToDisplay'       => 'Switch on to display',
    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/legend.blade.php
    //handled in the event type part in the /resources/views/clubEventView.blade.php part

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/monthCell.blade.php
    'internalSurvey'        => 'Internal survey',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/navigation.blade.php
    'today'                 => 'Day',
    'month'                 => 'Month',
    'week'                  => 'Week',
    'toggleTheme'           => 'Toggle theme',

    //not translated the term 'logs'
    'manageClub'            => 'Manage clubs',
    'manageShiftType'       => 'Manage shift types',
    'manageSections'        => 'Manage sections',
    // TODO use Job for Service - german: Dienst maybe change to Shift - Schicht

    'manageTemplates'        => 'Manage templates',

    //create button text
    'createNewEvent'           => 'Create new event',
    'createNewSurvey'          => 'Create new survey',

    //Member types
    'candidate'             => 'Candidate',
    'veteran'               => 'Veteran',
    'ex-member'             => 'ex-Member',
    'ex-candidate'          => 'ex-Candidate',
    'active'                => 'Active',
    'external'              => 'External',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/scheduleEntryName.blade.php
    'IDoIt'                 => 'I will do it!', //Ich mach's!

    // /resources/views/partials/scheduleEntryStatus.blade.php
    'jobFree'               => 'Shift free',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/statisticsLeaderboards.blade.php
    'totalShifts'           => 'Shifts',
    'leaderBoards'          => 'Leaderboard',
    'allClubs'              => 'All',

    // /resources/view/partials/statistics/amountOfShiftsDisplay.blade.php
    'shiftsInOtherSection'      => 'Shifts in other sections',
    'shiftsInOwnSection'        => 'Shifts in own section',
    'floodShifts'               => 'How many times has it been cleaned up',

    // /resources/views/partials/clubStatistics.blade.php
    'infoFor'               => 'Member statistics',

    // /resources/views/partials/personalStatistics.blade.php

    // /resources/views/statisticsView.blade.php
    'monthStatistic'       => 'monthly Statistics',
    'yearStatistic'       => 'yearly Statistics',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/surveyAnswerStatus.blade.php
    //no new strings

    //-----------------------------------------------------------------------------------------------------------
    // /resources/views/partials/surveyForm.blade.php
    'showOnlyForLoggedInMember' => 'Only visible to logged in users',
    'showResultsOnlyForCreator' => 'Results are only visible to the survey creator',
    'showResultsAfterFillOut'   => 'Results are visible after filling out the survey',

    'passwordSetOptional'       => 'Setting a password is optional',

    //Answer and Question options
    'answerOption'          => 'Answer options',
    'question'              => 'Question',

    //Questionoptions
    'freeText'              => 'Free Text',
    //TODO Line ~299 Freitext throws buggy string with trans(), maybe because of Javascript
    'checkbox'              => 'Checkbox',
    'dropdown'              => 'Dropdown',

    'required'              => 'required',
    'addAnswerOption'       => 'Add an answer option',
    'addQuestion'           => 'Add a question',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellFull.blade.php
    'hide'                  => 'Hide',

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellHidden.blade.php
    'moreDetailsAfterLogInMessage' => 'More details are accessible to members after logging in.',
    // 'moreDetailsAfterLogInMessage2' => 'after logging in.',
    // Merged with line above but there is now way to break the line (format is still ok)
    // ToDo find a solution for breaking lines

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellProtected.blade.php
    // no new strings

    //-----------------------------------------------------------------------------------------------------------

    // /resources/views/partials/weekCellSurvey.blade.php
    //no new strings

    //-----------------------------------------------------------------------------------------------------------

    // /public/js ToDo add these strings
    //Maybe To do translate Javascript, tricky, " " can throw error messages
    // /public/js/vedst-script.js
    'errorMessageForgotFilter'              => 'The filter wasn not set! Please add at least one section this event should be shown to.',
    'errorMessageEnterPasswordForShiftPlan' => 'Please enter the password for this schedule:',
    'shiftTime'                             => 'Shift time',
    //'hideTimes' already exists#

    'weekStart'                             => 'Monday - Sunday',
    'hideTakenShifts'                       => 'Only EMPTY shifts',

    //-----------------------------------------------------------------------------------------------------------

    // Controller  ToDo add these strings
    // ShiftController
    // action description
    'addedComment'              => 'Comment added',
    'deletedComment'            => 'Comment deleted',
    'changedComment'            => 'Comment changed',

    'errorMessageAbortDeletion' => 'Error: can not delete, schedule entry does not exist.',

    //SurveyController
    'messageSurveyDeleted'      => 'Survey deleted.',

    //SurveyAnswerController
    'messageSuccessfulVoted'    => 'Successfully voted!',
    'messageSuccessfulDeleted'  => 'Successfully deleted!',

    //-----------------------------------------------------------------------------------------------------------

    // placeholder strings (e.g. used in buttons or text fields)

    /*
    | instead of { trans('message.key' }} use  Lang::get('message.key') in the view
    */

    //ClubEvent
    'cantDeleteEvent'               => 'Only administrators, CLs and Marketing members belonging to the event\'s section can delete an event.',
    'cantUpdateEvent'               => 'Only administrators, CLs and Marketing members belonging to the event\'s section, and the creator of the event can delete an event.',
    'addCommentHere'                => 'Add comment here',
    'enterPasswordHere'             => 'Enter password here',
    'placeholderTitleWineEvening'   => 'e.g. Wine evening', //'placeholderTitleWineEvening'
    'placeholderSubTitleWineEvening'=> 'e.g. Life is too short to drink bad wine', //'placeholderSubTitleWineEvening'
    'placeholderPublicInfo'         => 'e.g. Tickets only in presale',
    'placeholderPrivateDetails'     => 'e.g. DJ-table is needed',

    //Survey
    'addAnswerHere'                 => 'Add answer here',
    'createSurvey'                  => 'Create survey', //Button text

    //Partials
    //Navigation
    'clubNumber'                    => 'Clubnumber',
    'password'                      => 'Password',
    'logIn'                         => 'Login',
    'logOut'                        => 'Logout',
    'light'                         => 'Light',
    'dark'                          => 'Dark',
    //ShiftName
    '=FREI='                        => '=FREI=', //not used yet

    //ShiftTitle
    'optional'                      => 'Optional',
    'optionalShort'                 => 'Opt.',

    //surveyForm
    'placeholderSurveyTitle'        => 'Title:',
    'placeholderTitleSurvey'        => 'e.g. Participation at the club trip',
    'placeholderDescription'        => 'Description:',
    'placeholderActiveUntil'        => 'Active until:',

    //Misc
    'guest'                         => 'Guest',
    'accessDenied'                  => 'Wait a second... Only logged-in members with verified credentials can get in here! Now go log in, or come to the next meeting and become a member.',
    'filter'                        => 'filter',
    'submit'                        => 'submit',


    ////////////////
    // Management //
    ////////////////

    'management'                    => 'Management',
    'shiftType'                       => 'Shift type',
    'shiftTypes'                      => 'Shift types',
    'replaceAll'                    => 'replace all',
    'shift'                         => 'Shift',
    'start'                         => 'Start',
    'end'                           => 'End',
    'weight'                        => 'Weight',
    'actions'                       => 'Actions',
    'deleteConfirmation'            => 'Do you really want to delete this shift type:',
    'warningNotReversible'          => 'This action is not reversible!',
    'editDetails'                   => 'Edit details',
    'deleteThisShiftType'             => 'Delete',
    'editShiftType'                 => 'edit Shift type',

    'reset'                         => 'Reset',
    'update'                        => 'Save changes',
    'delete'                        => 'delete it',
    'shiftTypeNeverUsed'              => 'This shift type was never used... Quite sad, isn\'t it? Maybe you should just ',
    'shiftTypeUsedInFollowingEvents'  => 'This shift type is used in the following events. To remove it you should substitute each of its instances with another shift type.',
    'shiftTypeUsedInFollowingTemplates'  => 'This shift type is used in the following templates. To remove it you should substitute each of its instances with another shift type.',
    'event'                         => 'Event',
    'date'                          => 'Date',
    'substituteThisInstance'        => 'Substitute with...',

    'cantTouchThis'                 => 'Nice try! You can\'t change this, ask saction management or marketing ;)',
    'cantBeBlank'                   => 'This values can\'t be empty.',
    'nonNumericStats'               => 'Statistical weight must be entered with numbers ;)',
    'negativeStats'                 => 'Statistical weight can\'t be negative.',
    'changesSaved'                  => 'Changes saved successfully.',
    'deleteFailedShiftTypeInUse'    => 'Shift type was not deleted, because it\'s in use. Here you can change that.',
    'adminsOnly'                    => 'Sorry, but this function can only used by Administrators',


    ////////////////////////
    // Section management //
    ////////////////////////
    'color'                         => 'Color',
    'sectionExists'                 => 'A section with this name already exits, please enter a diffrent one!',
    'newSection'                    => 'New section',
    'createSection'                 => 'Create section',
    'privateClubName'               => 'Should my clubname be hidden from guests?',
    'privateClubNameYes'            => 'Yes, hide my clubname from guests',
    'privateClubNameNo'             => 'No, everyone can see my clubname',
    'privateClubNameNull'           => 'Use my section\'s default setting',
    'upcomingShifts'                => 'Upcoming shifts',

    //////////
    // ICal //
    //////////

    'icalfeeds'                     =>  'Calendarfeed using iCal',
    'publishEvent'                  =>  'Publish event to calendar feed',
    'unpublishEvent'                =>  'Remove event from calendar feed',
    'createAndPublish'              =>  'Create and publish event',
    'createUnpublished'             =>  'Create event without publishing',
    'eventIsPublished'              =>  'Event is published and added to calendar feed',
    'eventIsUnpublished'            =>  'Event is not published and isn\'t visible in calendar feed',
    'confirmPublishingEvent'        =>  'Are you sure you want to publish this event to calendar feed?',
    'confirmUnpublishingEvent'      =>  'Are you sure you want to remove this event from the calendar feed?',
    'iCal'                          =>  'iCal',

    /////////////////////////
    // Template management //
    /////////////////////////

    'search'                        => 'Search',
    'facebookNeeded'                => 'Facebook needed',
    'createTemplate'                => 'Create template',

    /////////////////////////
    // User management     //
    /////////////////////////
    'status'                        => 'Status',
    'editUser'                      => 'Edit user',
    'availableRoles'                => 'Available roles',
    'activeRoles'                   => 'Active roles',
    'changesWereReset'              => 'Your changes were reset. Please apply them again before submitting.',
    'roleManagement'                => 'Manage roles',
    'changesSaved'                  => 'Changes saved.',
    'sectionChanged'                => 'You changed the section of this user. Remember to also update his roles.',

    /////////////////
    // Legal stuff //
    /////////////////

    'privacyPolicy'     => 'Privacy Policy',
    'impressum'         => 'Imprint',

    'agreeWithPrivacy'  => 'You must read and agree with our Privacy Policy in order to use this website. Please allow us to process your data by clicking on the button down below.',
    'privacyAccepted'   => 'Thank you, now you can continue using Lara',
    'fatalErrorUponSaving' => 'Something went wrong. Please try again later or contact an administrator for support.',
    'privacyAgree'      => 'I Agree with Lara\'s Privacy Policy',
    'waitOneSecond'     => 'Just a second...',

    ///////////////////
    // Personal Page //
    ///////////////////
    'userPersonalPage' => 'Personal page',
    'settings'         => 'Settings',

    ///////////////////
    // 2fa           //
    ///////////////////
    '2fa'             => 'two factor authentication',
    '2fa.setup'       => 'Set up your two factor authentication by scanning the QR code below. Alternatively, you can use the code',
    '2fa.verifyWorking' => 'To ensure that the setup is complete, please enter the current code.',
    '2fa.unregister'       => 'remove two factor authentication',

];
