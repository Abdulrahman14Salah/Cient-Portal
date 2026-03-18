<?php

namespace App\Modules\Application\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $guarded = [];

    public function payments()
    {
        return $this->hasMany(\App\Modules\Payment\Models\Payment::class);
    }
}
