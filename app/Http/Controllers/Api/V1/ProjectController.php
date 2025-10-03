<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\File;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProjectResource::collection(
            Project::with('image')->orderBy('order')->orderByDesc('title')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'handle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:2048'],
            'order' => ['nullable', 'integer'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $project = new Project();
        $project->title = $validated['title'];
        $project->handle = $validated['handle'] ?? Str::slug($validated['title']);
        $project->description = $validated['description'] ?? null;
        $project->url = $validated['url'] ?? null;
        $project->order = $validated['order'] ?? 0;

        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image');

            $disk = config('filesystems.default', 'local');
            $ext = $uploadedFile->getClientOriginalExtension();
            $originalName = $uploadedFile->getClientOriginalName();
            $objectKey = 'projects/' . $project->handle . '/' . Str::uuid() . ($ext ? ('.' . $ext) : '');

            // Store the file
            Storage::disk($disk)->putFileAs(
                dirname($objectKey),
                $uploadedFile,
                basename($objectKey)
            );

            // Create the File record
            $file = File::create([
                'disk' => $disk,
                'bucket' => config("filesystems.disks.$disk.bucket"),
                'object_key' => $objectKey,
                'filename' => $originalName,
                'extension' => $ext ?: null,
                'uploaded_at' => now(),
            ]);

            // Link to project
            $project->project_image_id = $file->id;
        }

        $project->save();

        return (new ProjectResource($project->load('image')))
            ->response()
            ->setStatusCode(201);
    }
}
