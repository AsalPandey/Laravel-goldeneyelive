<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class CheckImageSize
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $files = $request->allFiles();

        if (empty($files)) {
            return $next($request);
        }

        foreach ($files as $file) {
            // Check if it's an image (mime type check is safer)
            if (str_starts_with($file->getMimeType(), 'image/')) {
                $limit = SiteSetting::getValue('image_size_limit', 2048);
                if ($file->getSize() > ($limit * 1024)) {
                    Alert::error('File Too Large', '"'.$file->getClientOriginalName().'" is '.number_format($file->getSize() / 1024, 0).'KB. Maximum allowed size is '.$limit.'KB for site performance.')
                        ->footer('Please compress your image at <a href="https://tinypng.com" target="_blank">tinypng.com</a> before uploading.');

                    return back();
                }
            }
        }

        return $next($request);
    }
}
