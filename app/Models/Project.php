<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\SortableTrait;

class Project extends Model
{
    use SortableTrait;

    public array $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'title', 'description', 'url', 'project_image_id',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $project) {
            // Always ensure handle is a slug of the title on create/update
            if ($project->isDirty('title') || blank($project->handle)) {
                $project->handle = Str::slug((string) $project->title);
            }
        });
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'project_image_id');
    }
}
