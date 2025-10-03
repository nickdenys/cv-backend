<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'url'      => $this->temporaryUrl(5, [
                'ResponseContentDisposition' => 'inline; filename="'.$this->filename.'"',
                'ResponseContentType'        => $this->mime ?? 'application/octet-stream',
            ]),
        ];
    }
}
