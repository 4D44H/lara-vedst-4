<?php

namespace Lara;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Table to store user specific settings, e.g languages
 *
 * @property String language
 * @property Integer userId
 * @property String view_mode
 */
class Settings extends Model
{
    protected $table = 'settings';
    
    protected $fillable = ['language', 'userId', 'view_mode'];
    
    public function applyToSession()
    {
        collect($this->getFillable())->filter(function ($attr) {
            return $attr != 'userId';
        })->each(function ($attr) {
            if (isset($this[$attr]) && !is_null($this[$attr])) {
                session([$attr, $this[$attr]]);
            }
        });
    }
}
