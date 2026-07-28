<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    use InteractsWithAssets;

    public function index(): JsonResponse
    {
        $path = public_path('site/img');
        $images = [];

        if (File::isDirectory($path)) {
            foreach (File::allFiles($path) as $file) {
                $relativePath = str_replace(public_path().DIRECTORY_SEPARATOR, '', $file->getRealPath());

                $images[] = [
                    'name' => $file->getFilename(),
                    'path' => str_replace('\\', '/', $relativePath),
                    'size' => number_format($file->getSize() / 1024, 2).' KB',
                ];
            }
        }

        usort($images, fn (array $left, array $right): int => strcasecmp($left['name'], $right['name']));

        return response()->json(['images' => $images]);
    }

    public function store(Request $request): RedirectResponse
    {
        $imageLimit = (int) SiteSetting::getValue('image_size_limit', 2048);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
        ]);

        $this->uploadAsset($request->file('image'));

        cache()->forget('site_used_assets');

        return back()->with('success', 'The image is now available in the media library.');
    }
}
