<?php

use Illuminate\Support\Facades\Route;
use Lara\Http\Controllers\AdminController;
use Lara\Http\Controllers\ClubController;
use Lara\Http\Controllers\ClubEventController;
use Lara\Http\Controllers\DateController;
use Lara\Http\Controllers\EventApiController;
use Lara\Http\Controllers\IcalController;
use Lara\Http\Controllers\LanguageController;
use Lara\Http\Controllers\LegalController;
use Lara\Http\Controllers\LoginController;
use Lara\Http\Controllers\LogViewerController;
use Lara\Http\Controllers\MonthController;
use Lara\Http\Controllers\PersonController;
use Lara\Http\Controllers\ScheduleController;
use Lara\Http\Controllers\SecondFactorController;
use Lara\Http\Controllers\ShiftTypeController;
use Lara\Http\Controllers\StatisticsController;
use Lara\Http\Controllers\SurveyController;
use Lara\Http\Controllers\TemplateController;
use Lara\Http\Controllers\UserController;
use Lara\Http\Controllers\UserPersonalPageController;
use Lara\Http\Controllers\ViewModeController;
use Lara\Http\Controllers\WeekController;
use Lara\Http\Controllers\YearController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
|--------------------------------------------------------------------------
| Default Laravel routes for reference
|--------------------------------------------------------------------------
|
| Route::get('/', function () {
|     return view('welcome');
|  });
|
| Route::get('/', [WelcomeController::class, 'index']);
|
| Route::get('home', [HomeController::class, 'index']);
|
| Route::controllers([
| 	'auth' => 'Auth\AuthController',
| 	'password' => 'Auth\PasswordController',
| 	]);
|
*/


/*
|--------------------------------------------------------------------------
| Global Patterns
|--------------------------------------------------------------------------
*/

use Lara\Http\Middleware\ClOnly;

Route::pattern('id',     '[0-9]+');
Route::pattern('year',     '[2][0][0-9][0-9]');
Route::pattern('month',    '[0][1-9]|[1][0-2]');
Route::pattern('week',     '[0-5][0-9]');
Route::pattern('day',     '[0-3][0-9]');


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

// LOG VIEWER
Route::get('logs',                                 [LogViewerController::class, 'index'])
    ->middleware('checkRoles:admin');


// DEFAULT
Route::get('/',                                 [MonthController::class, 'currentMonth']);
Route::get('/calendar/',                        [MonthController::class, 'currentMonth']);


// AUTHENTIFICATION
Route::get('login',                                [MonthController::class, 'currentMonth'])->name('login');
Route::get('logout',                             [LoginController::class, 'doLogout'])->name('logout');

Route::post('login',                             [LoginController::class, 'doLogin'])->name('login.post');
Route::post('logout',                             [LoginController::class, 'doLogout'])->name('logout.post');

//2fa
Route::get('2fa',                               [SecondFactorController::class, 'index'])->name("lara.2fa");
Route::post("2fa",                              [SecondFactorController::class, 'verify'])->name('verify.2fa');


// TIMESTAMP
Route::get('updates/{id}',                         [ScheduleController::class, 'getUpdates']);


// YEAR
Route::get('/calendar/year',                    [YearController::class, 'currentYear']);
Route::get('/calendar/{year}',                    [YearController::class, 'showYear']);


// MONTH
Route::get('/calendar/month',                    [MonthController::class, 'currentMonth']);
Route::get('/calendar/{year}/{month}',            [MonthController::class, 'showMonth']);


// WEEK
Route::get('/calendar/week/{id}',                 [WeekController::class, 'showId']);
Route::get('/calendar/week',                     [WeekController::class, 'currentWeek']);
Route::get('/calendar/{year}/KW{week}',         [WeekController::class, 'showWeek']);


// DATE
Route::get('/calendar/today',                    [DateController::class, 'currentDate']);
Route::get('/calendar/{year}/{month}/{day}',    [DateController::class, 'showDate']);


// CREATE
Route::get('event/{year?}/{month?}/{day?}/{templateId?}/create', [ClubEventController::class, 'create']);
Route::post('event/{year?}/{month?}/{day?}/{templateId?}/create', [ClubEventController::class, 'create']);


// AJAX calls
Route::get('person/{query?}',                 [PersonController::class, 'index']);
Route::get('club/{query?}',                 [ClubController::class, 'index']);
Route::get('statistics/person/{query?}',     [StatisticsController::class, 'shiftsByPerson'])
    ->middleware('rejectGuests');
Route::get('shiftTypes/{query?}',             [ShiftTypeController::class, 'find']);
Route::get('monthViewShifts/{year}/{month}', [MonthController::class, 'markShiftsOfCurrentUser']);
Route::get('monthViewTable/{year}/{month}', [MonthController::class, 'monthViewTable']);

// additional route to store a SurveyAnswer
Route::post('survey/{survey}/storeAnswer', [SurveyController::class, 'storeAnswer']);

// Language
Route::get('lang/{lang}', [LanguageController::class, 'switchLang'])->name('lang.switch');

Route::get('lang', function () {
    return response()->json(['language' => session('applocale')]);
});
// ViewMode
Route::get('viewmode/{mode}', [ViewModeController::class, 'switchMode'])->name('viewMode.switch');

// RESTful RESOURCES
Route::resource('shiftType',         'ShiftTypeController')
    ->middleware('checkRoles:admin,clubleitung,marketing');
Route::resource('shift',             'ShiftController',             ['except' => ['index', 'create', 'store', 'edit', 'destroy']]);
Route::resource('schedule',         'ScheduleController',         ['except' => ['index', 'create', 'store', 'edit', 'destroy']]);
Route::resource('event',             'ClubEventController',         ['except' => ['index']]);
Route::resource('person',             'PersonController',         ['only'   => ['index']])
    ->middleware('rejectGuests');
Route::resource('club',             'ClubController',             ['only'   => ['index']]);
Route::resource('survey',            'SurveyController',            ['except' => ['index']]);
Route::resource('survey.answer',     'SurveyAnswerController',     ['only'   => ['show', 'store', 'update', 'destroy']]);
Route::resource('section',             'SectionController');

// STATISTICS
Route::get('/statistics/month/{year?}/{month?}',    [StatisticsController::class, 'showStatistics'])->middleware('rejectGuests');
Route::get('/statistics/year/{year?}',    [StatisticsController::class, 'showYearStatistics'])->middleware('rejectGuests');


// JSON EXPORT - RETURNS EVENTS METADATA
Route::get('export/{clb_id}/{date_start}/{nr_of_events}', function ($clb_id, $date_start, $nr_of_events) {

    $results = \Lara\ClubEvent::where('plc_id', '=', $clb_id)
        ->where('evnt_is_private', '=', '0')
        ->where('evnt_date_start', '>=', $date_start)
        ->whereIn('evnt_type', [0, 2, 3])
        ->orderBy('evnt_date_start')
        ->orderBy('evnt_time_start')
        ->take((0 <= $nr_of_events) && ($nr_of_events <= 20) ? $nr_of_events : 20)    // setting output size range to 0-20
        ->get(['id', 'evnt_title', 'evnt_date_start', 'evnt_time_start']);

    return response()->json($results, 200, [], JSON_UNESCAPED_UNICODE);
});

// ICAL: CALENDAR FEED
Route::get('/ical/public/allevents',                             [IcalController::class, 'allPublicEvents'])->name("icalallevents");
Route::get('/ical/feed/{location}/{with_private_info?}',         [IcalController::class, 'events']);
Route::get('/ical/location/{location}/{with_private_info?}',     [IcalController::class, 'events']);
Route::get('/ical/events/user/{club_id}/{alarm?}',                 [IcalController::class, 'userScheduleWithAlarm']);
// Disabling iCal until fully functional.
// Route::get('/ical/links', 										[IcalController::class, 'generateLinks']);
Route::get('/ical/event/{evt_id}',                                [IcalController::class, 'singleEvent']);
Route::get('/ical/event/{id}/publish',                             [IcalController::class, 'togglePublishState'])->name("togglePublishState");

// Lara Upgrade
Route::get('/update',                                         [AdminController::class, 'startUpdateProcess'])->name('lara.update');

//Templates
Route::get('/templates',                                      [TemplateController::class, 'index'])->name('template.overview');
Route::get('/template/{id}',                                  [TemplateController::class, 'show'])->name('template.edit');
Route::post('/edit/template/{id}',                                 [TemplateController::class, 'store'])->name('template.update');
Route::post('/delete/template/{id}',                          [TemplateController::class, 'destroy'])->name('template.delete');
Route::get('/create/template/',                               [TemplateController::class, 'create'])->name('template.create');

//ShiftType
Route::post('/overrideShiftType/',                              [ShiftTypeController::class, 'overrideShiftType'])->name('shiftTypeOverride');
Route::post('/completeOverrideShiftType',                       [ShiftTypeController::class, 'completeOverrideShiftType'])->name('completeOverrideShiftType');
Route::get('/shiftTypeSearch/{filter?}',                               [ShiftTypeController::class, 'index'])->name('shiftTypeSearch');
Route::post('/searchShiftType/',                                 [ShiftTypeController::class, 'search'])->name('searchShiftType');



Route::get('register', ['as' => 'register', 'uses' => 'Auth\RegisterController@showRegistrationForm'])
    ->middleware('checkRoles:admin,clubleitung');
Route::post('register', ['as' => 'register.post', 'uses' => 'Auth\RegisterController@register'])
    ->middleware('checkRoles:admin,clubleitung');

// Password Reset Routes...
Route::get('password/reset', ['as' => 'password.reset', 'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
Route::post('password/email', ['as' => 'password.email', 'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::get('password/reset/{token}', ['as' => 'password.reset.token', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset', ['as' => 'password.reset.post', 'uses' => 'Auth\ResetPasswordController@reset']);

// Usermanagement
Route::post('/user/updateData/{user}', [UserController::class, 'updateData'])->name('user.updateData');
Route::resource('user', 'UserController');
Route::get('/personalpage', [UserPersonalPageController::class, 'showPersonalPage'])->name('user.personalpage');
Route::post('/updatePersonalSettings', [UserPersonalPageController::class, 'updatePerson'])->name('user.updatePersonalSettings');
Route::post('/registerAuthenticator', [UserPersonalPageController::class, 'registerGoogleAuth'])->name('user.registerGoogleAuth');
Route::post('/unregisterAuthenticator', [UserPersonalPageController::class, 'unregisterGoogleAuth'])->name('user.unregisterGoogleAuth');
Route::post('/user/deleteUer/{userId}', [UserController::class, 'destroy'])->name('user.delete');

Route::get('/password/change', ['as' => 'password.change', 'uses' => 'Auth\PasswordChangeController@showChangePasswordForm'])
    ->middleware('auth');
Route::post('/password/change', ['as' => 'password.change.post', 'uses' => 'Auth\PasswordChangeController@changePassword'])
    ->middleware('auth');

// LEGAL
Route::get('/privacy',                    [LegalController::class, 'showPrivacyPolicy'])->name('lara.privacy');
Route::get('/impressum',                [LegalController::class, 'showImpressum'])->name('lara.impressum');
Route::post('userAgreesPrivacy', [UserController::class, 'agreePrivacy'])->name('user.agreePrivacy');

// API
Route::get('/api/events/{sectionName}', [EventApiController::class, 'getEventsFor']);
