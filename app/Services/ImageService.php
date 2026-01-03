<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Upload and attach an image to a model
     *
     * @param Model $model The model to attach the image to
     * @param UploadedFile|null $file The uploaded file
     * @param string $type Image type (e.g., 'avatar', 'profile', 'cover')
     * @param string $disk Storage disk (default: 'public')
     * @return Image|null
     */
    public function uploadAndAttach(
        Model $model,
        ?UploadedFile $file,
        string $type = 'avatar',
        string $disk = 'public'
    ): ?Image {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Delete existing image of the same type if exists
        $this->deleteByType($model, $type);

        // Generate unique filename
        $filename = $this->generateFilename($file, $model, $type);

        // Store file in tenant-aware storage
        $path = $file->storeAs(
            $this->getStoragePath($model, $type),
            $filename,
            $disk
        );

        // Create image record
        $image = $model->images()->create([
            'path' => $path,
            'disk' => $disk,
            'type' => $type,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'alt' => $this->generateAltText($model, $type),
        ]);

        return $image;
    }

    /**
     * Delete image by type
     *
     * @param Model $model
     * @param string $type
     * @return bool
     */
    public function deleteByType(Model $model, string $type): bool
    {
        $image = $model->images()->where('type', $type)->first();

        if ($image) {
            // Delete file from storage
            if (Storage::disk($image->disk)->exists($image->path)) {
                Storage::disk($image->disk)->delete($image->path);
            }

            // Delete database record
            return $image->delete();
        }

        return false;
    }

    /**
     * Delete all images for a model
     *
     * @param Model $model
     * @return void
     */
    public function deleteAll(Model $model): void
    {
        $images = $model->images;

        foreach ($images as $image) {
            // Delete file from storage
            if (Storage::disk($image->disk)->exists($image->path)) {
                Storage::disk($image->disk)->delete($image->path);
            }
        }

        // Delete all database records
        $model->images()->delete();
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @param Model $model
     * @param string $type
     * @return string
     */
    protected function generateFilename(UploadedFile $file, Model $model, string $type): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $modelType = strtolower(class_basename($model));
        $modelId = $model->id ?? 'new';

        return "{$modelType}_{$modelId}_{$type}_{$timestamp}.{$extension}";
    }

    /**
     * Get storage path for the image
     *
     * @param Model $model
     * @param string $type
     * @return string
     */
    protected function getStoragePath(Model $model, string $type): string
    {
        $modelType = strtolower(class_basename($model));
        return "images/{$modelType}/{$type}";
    }

    /**
     * Generate alt text for the image
     *
     * @param Model $model
     * @param string $type
     * @return string
     */
    protected function generateAltText(Model $model, string $type): string
    {
        $modelName = $model->name ?? $model->email ?? 'User';
        return ucfirst($type) . " for {$modelName}";
    }
}
