<?php

namespace IICN\Notification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'title',
        'content',
        'image_url',
        'user_id',
        'condition',
        'data',
        'model_id',
        'model_type',
        'timezone',
        'send_date',
    ];

    protected $casts = [
        'data' => 'json',
        'send_date' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('notification.user_model'));
    }
}
