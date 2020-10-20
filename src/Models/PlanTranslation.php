<?php

namespace elsayed85\Subscriptions\Models;

use Illuminate\Database\Eloquent\Model;

class PlanTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_translations';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
