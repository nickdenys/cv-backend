<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\File;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->processImage($data);
        return $data;
    }

    private function processImage(array $data): array
    {
        if (! array_key_exists('project_image_id', $data) || empty($data['project_image_id'])) {
            return $data; // no image field present or empty
        }

        $value = $data['project_image_id'];

        // If already a File UUID, keep as-is (user didn't change upload in this session)
        if (is_string($value) && ! str_contains($value, '/')) {
            if (File::find($value)) {
                return $data;
            }
        }

        // If it's an array (defensive), take first path
        if (is_array($value)) {
            $value = $value[0] ?? null;
            if (!$value) return $data;
        }

        // At this point $value should be a path to either the existing object's object_key (unchanged) or a temp upload path.
        $currentObjectKey = $this->record->image?->object_key;
        if ($currentObjectKey && $value === $currentObjectKey) {
            // Unchanged image: reset to existing UUID so we don't duplicate
            $data['project_image_id'] = $this->record->project_image_id;
            return $data;
        }

        $disk = config('filesystems.default', 'local');
        if (!Storage::disk($disk)->exists($value)) {
            return $data; // path missing (nothing we can do)
        }

        $title = (string) ($data['title'] ?? $this->record->title ?? 'project');
        $slug = Str::slug($title);

        $originalName = basename($value);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $objectKey = 'projects/' . $slug . '/' . Str::uuid() . ($ext ? ('.' . $ext) : '');

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
