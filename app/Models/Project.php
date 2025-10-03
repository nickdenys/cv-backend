<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'title', 'description', 'url', 'order', 'project_image_id',
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'project_image_id');
    }
}
