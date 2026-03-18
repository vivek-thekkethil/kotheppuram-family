<?php

namespace Tests\Feature;

use App\Models\GalleryPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_multiple_gallery_photos(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'photos' => [
                UploadedFile::fake()->image('gallery-1.jpg'),
                UploadedFile::fake()->image('gallery-2.jpg'),
            ],
        ]);

        $response->assertRedirect();

        $photos = GalleryPhoto::all();

        $this->assertCount(2, $photos);

        foreach ($photos as $photo) {
            $this->assertStringStartsWith('uploads/gallery/gallery_', $photo->path);
            $this->assertFileExists(public_path($photo->path));

            if (File::exists(public_path($photo->path))) {
                File::delete(public_path($photo->path));
            }
        }
    }

    public function test_admin_can_delete_gallery_photo(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $directory = public_path('uploads/gallery');
        File::ensureDirectoryExists($directory);

        $fileName = 'delete-test.jpg';
        $path = 'uploads/gallery/'.$fileName;
        File::put(public_path($path), 'fake-image-content');

        $photo = GalleryPhoto::create([
            'path' => $path,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.gallery.delete', $photo));

        $response->assertRedirect();

        $this->assertDatabaseMissing('gallery_photos', [
            'id' => $photo->id,
        ]);
        $this->assertFileDoesNotExist(public_path($path));
    }
}
