<?php

namespace Lara\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use DateTime;
use DateInterval;

use Auth;

use Lara\Person;

use Lara\Http\Requests;
use Lara\Http\Controllers\Controller;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index( $query = NULL )
    {
        // Not showing everything to guests
        $user = Auth::user();
        if(!$user)
        {
            session()->put('message', config('messages_de.access-denied'));
            session()->put('msgType', 'danger');
            return redirect()->action([MonthController::class, 'showMonth'], ['year' => date("Y"), 'month' => date('m')]);
        }

        // if no parameter specified - empty means "show all"
        if ( is_null($query) ) {
            $query = "";
        }
        
        $columns = ['prsn_name',
            'prsn_ldap_id',
            'prsn_status',
            'clb_id',
            'id'];
        
        $givenNameQuery = Person::query()->whereHas('user', function ($subquery) use ($query) {
            return $subquery->where('givenname','like','%'. $query . '%');
        })->select($columns);
    
        $lastNameQuery = Person::query()->whereHas('user', function ($subquery) use ($query) {
            return $subquery->where('lastname','like','%'. $query . '%');
        })->select($columns);

        $persons =  Person::query()->whereNotNull( "prsn_ldap_id" )
                                // Look for autofill
                                ->where('prsn_name', 'like', '%' . $query . '%')
                                ->union($givenNameQuery)
                                ->union($lastNameQuery)
                                ->with('club')
                                ->with(['user' => function( $userQuery){
                                  return $userQuery->select(['givenname','lastname','person_id','on_leave','id']);
                                }])
                                ->orderBy('prsn_name')
                                ->get($columns);

        return response()->json($persons);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
