<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class File extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id','disk','bucket','object_key','filename','extension', 'uploaded_at'
    ];
    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $file) {
            if (blank($file->id)) {
                $file->id = (string) Str::uuid();
            }
        });
    }

    public function getUrl(): string
    {
        return Storage::disk($this->disk)->url($this->object_key);
    }
}
