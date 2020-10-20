<?php

namespace elsayed85\Subscriptions\Models;

class PlanFeatureTranslation extends model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_features_translations';
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
