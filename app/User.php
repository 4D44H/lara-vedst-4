<?php

namespace Lara;

use Illuminate\Notifications\Notifiable;
use Lara\Person;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // authentication related
        'name', 'email', 'password',
        // Lara related
        'section_id', 'person_id', 'status', 'group'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function person()
    {
        return $this->belongsTo('Lara\Person');
    }

    public function section()
    {
        return $this->belongsTo('Lara\Section');
    }

    public static function createFromPerson(Person $person)
    {
        return User::create([
            'name' => $person->prsn_name,
            'email' => $person->prsn_name . '@lara.il-sc.de',
            'section_id' => $person->club->section()->id,
            'person_id' => $person->id,
            'status' => $person->prsn_status,
            'group' => $person->club->clb_title
        ]);
    }

    public function is($permissions)
    {
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        return collect($permissions)
            ->contains($this->group);
    }
}
