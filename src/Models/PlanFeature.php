<?php

declare(strict_types=1);

namespace elsayed85\Subscriptions\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use elsayed85\Subscriptions\Services\Period;
use elsayed85\Subscriptions\Traits\BelongsToPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rinvex\Support\Traits\ValidatingTrait;

/**
 * elsayed85\Subscriptions\Models\PlanFeature.
 *
 * @property int                                                                                                $id
 * @property int                                                                                                $plan_id
 * @property string                                                                                             $slug
 * @property array                                                                                              $title
 * @property array                                                                                              $description
 * @property string                                                                                             $value
 * @property int                                                                                                $resettable_period
 * @property string                                                                                             $resettable_interval
 * @property int                                                                                                $sort_order
 * @property \Carbon\Carbon|null                                                                                $created_at
 * @property \Carbon\Carbon|null                                                                                $updated_at
 * @property \Carbon\Carbon|null                                                                                $deleted_at
 * @property-read \elsayed85\Subscriptions\Models\Plan                                                             $plan
 * @property-read \Illuminate\Database\Eloquent\Collection|\elsayed85\Subscriptions\Models\PlanSubscriptionUsage[] $usage
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature byPlanId($planId)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature ordered($direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereResettableInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereResettablePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\elsayed85\Subscriptions\Models\PlanFeature whereValue($value)
 * @mixin \Eloquent
 */
class PlanFeature extends Model implements TranslatableContract
{
    use Sluggable;
    use Translatable;
    use BelongsToPlan;
    use ValidatingTrait;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'plan_id',
        'slug',
        'value',
        'resettable_period',
        'resettable_interval',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'plan_id' => 'integer',
        'slug' => 'string',
        'value' => 'string',
        'resettable_period' => 'integer',
        'resettable_interval' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'validating',
        'validated',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes  = [
        'name',
        'description',
    ];

    public $translationForeignKey = "feature_id";


    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a
     * ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('elsayed85.subscriptions.tables.plan_features'));
        $this->setRules([
            'plan_id' => 'required|integer|exists:' . config('elsayed85.subscriptions.tables.plans') . ',id',
            'value' => 'required|string',
            'resettable_period' => 'sometimes|integer',
            'resettable_interval' => 'sometimes|in:hour,day,week,month',
        ]);
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * The plan feature may have many subscription usage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usage(): HasMany
    {
        return $this->hasMany(config('elsayed85.subscriptions.models.plan_subscription_usage'), 'feature_id', 'id');
    }

    /**
     * Get feature's reset date.
     *
     * @param string $dateFrom
     *
     * @return \Carbon\Carbon
     */
    public function getResetDate(Carbon $dateFrom): Carbon
    {
        $period = new Period($this->resettable_interval, $this->resettable_period, $dateFrom ?? now());

        return $period->getEndDate();
    }
}
