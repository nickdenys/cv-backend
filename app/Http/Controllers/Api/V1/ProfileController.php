<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $profile = Profile::findOrFail(1);
        return new ProfileResource($profile);
    }

    public function update(Request $request)
    {
        $profile = Profile::findOrFail(1);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'bio' => ['sometimes', 'string'],
            'links' => ['sometimes', 'array'],
            'links.*.title' => ['required_with:links', 'string', 'max:100'],
            'links.*.handle' => ['required_with:links', 'string', 'max:100'],
            'links.*.url' => ['required_with:links', 'url', 'max:2048'],
        ]);

        // Update scalar fields only if provided
        if (array_key_exists('title', $validated)) {
            $profile->title = $validated['title'];
        }
        if (array_key_exists('bio', $validated)) {
            $profile->bio = $validated['bio'];
        }
        if (array_key_exists('links', $validated)) {
            $profile->links = $validated['links'];
        }

        $profile->save();

        return (new ProfileResource($profile))
            ->response()
            ->setStatusCode(200);
    }
}
