<?php

/**
 * DO NOT CALL THIS CLASS "EVENT"
 * That name is reserved for Laravel events and will cause an error.
 *
 */

namespace Lara;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\HtmlString;
use Lara\Http\Middleware\MarkdownParser;
use Lara\Shift;
use PhpParser\Node\Scalar\String_;

/**
 * @property int evnt_type
 * @property string evnt_title
 * @property string evnt_subtitle
 * @property Carbon evnt_date_start
 * @property Carbon evnt_date_end
 * @property \DateTime evnt_time_start
 * @property \DateTime evnt_time_end
 * @property string external_id
 * @property boolean facebook_done
 * @property string event_url
 * @property double price_tickets_normal
 * @property double price_tickets_external
 * @property double price_normal
 * @property double price_external
 * @property int template_id
 * @property Carbon unlock_date
 * @property string evnt_public_info
 * @property string evnt_private_details
 * @property boolean canceled
 */
class ClubEvent extends Model
{
    use MarkdownParser;

    /**
     * The database table used by the model.
     *
     * @var $table string
     */
    protected $table = 'club_events';

    /**
     * The database columns used by the model.
     * This attributes are mass assignable.
     *
     * @var $fillable array
     */
    protected $fillable = [
        'evnt_type',            // 0  -> default -> "normales Programm"         Shade:    700
                                // 1  -> info only                              Shade:    500, always purple
                                // 2  -> highlight / special                    Shade:    900
                                // 3  -> live band / DJ / reading               Shade:    900
                                // 4  -> internal event                         Shade:    500
                                // 5  -> private party -> "Nutzung"             Shade:    500
                                // 6  -> cleaning -> "Fluten"                   Shade:    500
                                // 7  -> flyer / poster                         Shade:    300
                                // 8  -> tickets -> "Vorverkauf"                Shade:    300
                                // 9  -> internal task -> everything else       Shade:    500
                                // 10 -> outside event -> "Außenveranstaltung"  Shade:    900
                                // 11 -> buffet                                 Shade:    900
        'evnt_title',
        'evnt_subtitle',
        'plc_id',
        'evnt_date_start',
        'evnt_date_end',
        'evnt_time_start',
        'evnt_time_end',
        'evnt_public_info',
        'evnt_private_details',
        'evnt_is_private',
        'evnt_is_published',
        'external_id',
        'price_tickets_normal',
        'price_tickets_external',
        'price_normal',
        'price_external',
        'facebook_done',
        'event_url',
        'template_id',
        'creator_id',
        'was_manually_edited',
        'unlock_date',
        'canceled'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unlock_date' => 'datetime',
    ];



    /**
     * Get the corresponding section.
     * Looks up in table sections for that entry, which has the same id like plc_id of ClubEvent instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Section
     */
    public function section()
    {
        return $this->belongsTo('Lara\Section', 'plc_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Template
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }

    /**
     * Get the corresponding schedule.
     * Looks up in table schedules for that entry, which has the same evnt_id like id of ClubEvent instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Schedule
     *
     */
    public function getSchedule()
    {
        return $this->hasOne('Lara\Schedule', 'evnt_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|Schedule
     */
    public function schedule()
    {
        return $this->hasOne('Lara\Schedule', 'evnt_id', 'id');
    }

    /**
     * Check if the signed in user has a shift in the current event
     * Looks up in table Shifts for entries, which has the same scheduleId like id of ClubEvent instance.
     * This funtion is only used for highlighting the event in month view if the signed in user has a shift in it.
     *
     * @return Boolean
     */
    public function hasShift($person)
    {
        return $this->shifts->contains(function (Shift $shift) use ($person) {
            return $shift->person_id === $person->id;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough|Shift
     */
    public function shifts()
    {
        return $this->hasManyThrough('Lara\Shift', 'Lara\Schedule', 'evnt_id', 'schedule_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Section
     */
    public function showToSection()
    {
        return $this->belongsToMany('Lara\Section');
    }


    public function showToSectionIds()
    {
        return $this->showToSection()->get()->map(function ($section) {
            return $section->id;
        })->toArray();
    }


    public function showToSectionNames()
    {
        return $this->showToSection()->get()->map(function ($section) {
            return $section->title;
        })->sort()->toArray();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function creator()
    {
        return $this->belongsTo('Lara\User', 'creator_id');
    }

    /**
     * formats the content of evnt_public_info as html
     * @return HtmlString
     */
    public function publicInfoMd()
    {
        return $this->mdToHtmlCached($this->evnt_public_info);
    }

    /**
     * formats the content of evnt_private_details as html
     * @return HtmlString
     */
    public function privateDetailsMd()
    {
        return $this->mdToHtmlCached($this->evnt_private_details);
    }
}
