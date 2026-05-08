<?php

namespace App\Traits;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Notice;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait InteractsWithAssets
{
    /**
     * Protected assets that should never be deleted from the server.
     */
    protected array $protectedAssets = [
        'carousel-1.jpg', 'carousel-1.png', 'carousel-2.jpg', 'carousel-2.png',
        'logo.png', 'favicon.ico', 'placeholder.jpg', 'default.jpg', 'user.png',
        'team-1.jpg', 'team-2.jpg', 'team-3.jpg', 'team-4.jpg',
        'testimonial-1.jpg', 'testimonial-2.jpg', 'testimonial-3.jpg', 'testimonial-4.jpg',
        'cat-1.jpg', 'cat-2.jpg', 'cat-3.jpg', 'cat-4.jpg',
        'premium.png', 'about.jpg', 'founder.jpg', 'message-chairperson.jpg',
    ];

    /**
     * High-level handler for model images in controllers.
     * Supports both file uploads and manual paths from the vault.
     */
    protected function handleAssetUpload($request, string $fieldName, string $directory, ?string $oldPath = null): ?string
    {
        if ($request->hasFile($fieldName)) {
            return $this->uploadAsset($request->file($fieldName), $directory, $oldPath);
        }

        if ($request->filled($fieldName.'_path')) {
            $manualPath = $request->input($fieldName.'_path');
            $cleanPath = $this->normalizePublicAssetPath($manualPath);

            if ($cleanPath && File::exists(public_path($cleanPath))) {
                return $cleanPath;
            }

            // If it doesn't exist, we fallback to old path or null to avoid broken links
            return $oldPath;
        }

        return $oldPath;
    }

    /**
     * Handle file upload and return the relative path.
     */
    protected function uploadAsset($file, string $directory = 'site/img', ?string $oldPath = null): string
    {
        // Delete old asset if provided and not protected
        if ($oldPath) {
            $this->deleteAsset($oldPath);
        }

        $path = public_path($directory);
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $safeName = time().'_'.Str::random(5).'.'.$file->extension();
        $file->move($path, $safeName);

        return trim($directory, '/').'/'.$safeName;
    }

    /**
     * Delete an asset from the server if it's not protected.
     */
    protected function deleteAsset(?string $path): bool
    {
        $cleanPath = $this->normalizePublicAssetPath($path);

        if (! $cleanPath || $this->isProtectedAsset($cleanPath)) {
            return false;
        }

        $fullPath = public_path($cleanPath);
        $publicRoot = realpath(public_path());
        $realPath = realpath($fullPath);

        if (! $publicRoot || ! $realPath || ! str_starts_with(strtolower($realPath), strtolower($publicRoot.DIRECTORY_SEPARATOR))) {
            return false;
        }

        if (File::exists($fullPath)) {
            // Check if ANY other model or setting is still using this exact path
            if ($this->isAssetInUse($cleanPath)) {
                return false;
            }

            return File::delete($fullPath);
        }

        return false;
    }

    /**
     * Alias for deleteAsset for clearer model destruction intent.
     */
    protected function secureAssetDeletion(?string $path): bool
    {
        return $this->deleteAsset($path);
    }

    /**
     * Delete a replaced asset after the owning database record has been updated.
     */
    protected function deleteReplacedAsset(?string $oldPath, ?string $newPath): bool
    {
        if (! $oldPath || $oldPath === $newPath) {
            return false;
        }

        return $this->secureAssetDeletion($oldPath);
    }

    /**
     * Comprehensive check across all database tables to see if a path is still referenced.
     */
    protected function isAssetInUse(string $path): bool
    {
        $relativePath = $this->normalizePublicAssetPath($path);

        if (! $relativePath) {
            return true;
        }

        // 1. Direct Column Checks (Performance Optimized)
        if (SiteSetting::where('value', $relativePath)->exists()) {
            return true;
        }

        if (Course::where('photo', $relativePath)->exists()) {
            return true;
        }

        if (Teacher::where('photo', $relativePath)->exists()) {
            return true;
        }

        if (Testimonial::where('photo', $relativePath)->exists()) {
            return true;
        }

        if (BlogPost::where('image', $relativePath)->exists()) {
            return true;
        }

        if (Notice::where('image', $relativePath)->exists()) {
            return true;
        }

        if (CourseCategory::where('image', $relativePath)->exists()) {
            return true;
        }

        // 2. Rich Text Field Scanning (Security: Prevent breaking embedded images)
        $richTextFields = [
            Course::class => ['description', 'course_outline'],
            BlogPost::class => ['content'],
            Notice::class => ['subtitle'],
            Teacher::class => ['bio'],
            CourseCategory::class => ['description'],
            SiteSetting::class => ['value'],
        ];

        foreach ($richTextFields as $model => $fields) {
            foreach ($fields as $field) {
                // Use where(field, like, %path%) for efficient partial matching in HTML
                if ($model::where($field, 'like', "%{$relativePath}%")->exists()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if an asset is protected.
     */
    protected function isProtectedAsset(string $path): bool
    {
        $filename = basename($path);

        return in_array($filename, $this->protectedAssets);
    }

    protected function normalizePublicAssetPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = str_replace('\\', '/', str_replace(url('/'), '', $path));
        $path = parse_url($path, PHP_URL_PATH) ?: $path;
        $path = ltrim($path, '/');

        if ($path === '' || str_contains($path, '..') || preg_match('/^[A-Za-z]:/', $path)) {
            return null;
        }

        if (! Str::startsWith($path, 'site/img/')) {
            return null;
        }

        return $path;
    }
}
