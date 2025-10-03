<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = $this->whenLoaded('image', function () {
            return new FileResource($this->image);
        });

        return [
            'id' => $this->id,
            'title' => $this->title,
            'handle' => $this->handle,
            'description' => $this->description,
            'image' => $image,
            'url' => $this->url,
            'order' => $this->order,
            'updatedAt' => $this->updated_at,
        ];
    }
}
