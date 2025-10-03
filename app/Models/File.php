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

    public function temporaryUrl(int $minutes = 5, array $headers = []): string
    {
        return Storage::disk($this->disk)->temporaryUrl(
            $this->object_key,
            now()->addMinutes($minutes),
            $headers + ['ResponseContentDisposition' => 'inline; filename="'.$this->filename.'"']
        );
    }
}
