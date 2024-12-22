<?php

namespace Lara;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Gate;
use Lara\utilities\RoleUtility;

/**
 * @property string name
 * @property string email
 * @property string password
 * @property string status
 * @property string givenname
 * @property string lastname
 * @property \Illuminate\Database\Eloquent\Relations\belongsToMany|Role roles
 * @property bool is_name_private
 * @property Section section
 * @property Person person
 * @property Settings settings
 * @property Carbon on_leave
 * @property string google2fa_secret
 */
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
        'name', 'email', 'password', 'google2fa_secret',
        // Lara related
        'section_id', 'person_id', 'status', 'givenname', 'lastname', 'is_name_private', 'on_leave'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'on_leave' => 'datetime',
        'is_name_private' => 'boolean'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Person
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Section
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany|Role
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public static function createNew($data)
    {
        // workaround, since most of the legacy depends on an LDAP id being present
        $newLDAPId = key_exists('prsn_ldap_id', $data) ? $data['prsn_ldap_id'] : DB::table('persons')->select(DB::raw('max(convert( prsn_ldap_id, unsigned integer)) ldap_id'))->first()->ldap_id + 1;;

        $section = Section::find($data['section']);
        $person = Person::create([
            'prsn_name' => $data['name'],
            'prsn_ldap_id' => $newLDAPId,
            'prsn_status' => $data['status'],
            'clb_id' => $section->club()->id,
            'prsn_uid' => hash("sha512", uniqid())
        ]);

        $user = User::create([
            'name' => $data['name'],
            'givenname' => $data['givenname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'status' => $data['status'],
            'section_id' => $data['section'],
            'group' => $section->title,
            'person_id' => $person->id
        ]);

        RoleUtility::assignPrivileges(
            $user,
            $section,
            RoleUtility::PRIVILEGE_MEMBER
        );

        return $user;
    }

    public static function createFromPerson(Person $person)
    {
        if ($person->user) {
            return $person->user;
        }

        if (!$person->club) {
            return NULL;
        }

        if (!$person->club->section()) {
            return NULL;
        }
        $oldStatus = $person->prsn_status;

        $newStatus = in_array($oldStatus, Status::ALL) ? $oldStatus : Status::MEMBER;
        $user = User::create([
            'name' => $person->prsn_name,
            'section_id' => $person->club->section()->id,
            'person_id' => $person->id,
            'status' => $newStatus
        ]);

        $person->prsn_status = $newStatus;
        $person->save();
        RoleUtility::assignPrivileges(
            $user, $person->club->section(),
            RoleUtility::PRIVILEGE_MEMBER
        );

        $person->user = $user;

        return $user;
    }

    public function isAn(...$permissions)
    {
        return $this->roles()->whereIn('name', $permissions)->exists();
    }

    public function hasPermissionsInSection(Section $section, ...$permission)
    {
        return $this->isAn(RoleUtility::PRIVILEGE_ADMINISTRATOR) ||
            $this->roles()
                ->whereIn('name', $permission)
                ->where('section_id', '=', $section->id)
                ->exists();
    }

    /**
     * @param Role $role
     * @return boolean user has role
     */
    public function hasPermission(Role $role)
    {
        return $this->roles()->where('id', '=', $role->id)->exists();
    }

    /**
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection|Role
     */
    public function getRolesOfType($type)
    {
        return $this->roles()->where('name', '=', $type)->get();
    }

    /**
     * @param string $type
     * @return Collection|int
     */
    public function getSectionsIdForRoles($type)
    {
        return $this->getRolesOfType($type)->map(function (Role $role) {
            return $role->section_id;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Settings
     */
    public function settings()
    {
        return $this->hasOne('Lara\Settings');
    }

    public function fullName()
    {
        if (Gate::allows('accessInformation', $this)) {
            return $this->givenname . " " . $this->lastname;
        }
        return "";
    }

    /**
     * Ecrypt the user's google_2fa secret.
     *
     * @param string $value
     * @return void
     */
    public function setGoogle2faSecretAttribute($value)
    {
        $savingValue = $value;
        if (!empty($value)) {
            $savingValue = encrypt($value);
        }
        $this->attributes['google2fa_secret'] = $savingValue;
    }

    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param string $value
     * @return string
     */
    public function getGoogle2faSecretAttribute($value)
    {
        if (!empty($value)) {
            return decrypt($value);
        }
        return $value;
    }
}
