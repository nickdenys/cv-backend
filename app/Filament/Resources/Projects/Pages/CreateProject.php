<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\File;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->processImage($data);
        return $data;
    }

    private function processImage(array $data): array
    {
        if (empty($data['project_image_id'])) {
            return $data; // no image provided
        }

        $value = $data['project_image_id'];
        // If it's already a File id (uuid) existing in DB, keep as-is
        if (is_string($value) && File::find($value)) {
            return $data; // existing image retained
        }

        // If it's an array (defensive), take first path
        if (is_array($value)) {
            $value = $value[0] ?? null;
            if (!$value) return $data;
        }

        $disk = config('filesystems.default', 'local');

        // Ensure uploaded path exists on disk (it should have been placed there by Livewire temp handling / FileUpload component)
        if (!Storage::disk($disk)->exists($value)) {
            return $data; // nothing to do
        }

        $title = (string) ($data['title'] ?? 'project');
        $slug = Str::slug($title);

        $originalName = basename($value);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $objectKey = 'projects/' . $slug . '/' . Str::uuid() . ($ext ? ('.' . $ext) : '');

        // Move (copy + delete) to final location matching API logic
        $stream = Storage::disk($disk)->readStream($value);
        Storage::disk($disk)->put($objectKey, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
        Storage::disk($disk)->delete($value);

        $file = File::create([
            'disk' => $disk,
            'bucket' => config("filesystems.disks.$disk.bucket"),
            'object_key' => $objectKey,
            'filename' => $originalName,
            'extension' => $ext ?: null,
            'uploaded_at' => now(),
        ]);

        $data['project_image_id'] = $file->id;

        return $data;
    }
}
