<?php

namespace Tests\Unit\Services;

use App\Models\Event;
use App\Models\EventFile;
use App\Models\EventRecord;
use App\Models\User;
use App\Services\EventFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventFileServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_uploaded_file_returns_array(): void
    {
        Storage::fake('local');

        $user = User::create([
            'username' => 'file-test-user',
            'password' => bcrypt('Password@123'),
        ]);
        $event = Event::create([
            'user_id' => $user->id,
            'title' => '文件上传测试',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $record = $event->records()->create([
            'user_id' => $user->id,
            'process' => null,
            'result' => null,
        ]);

        $request = Request::create('/', 'POST', [
            'process_image_keys' => json_encode(['key-1']),
        ]);
        $request->files->set('process_images', UploadedFile::fake()->image('test.jpg'));

        $service = $this->app->make(EventFileService::class);
        $result = $service->storeRecordUploads($event, $record, $user, $request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('process', $result);
        $this->assertArrayHasKey('result', $result);
        $this->assertIsArray($result['process']);
        $this->assertCount(1, $result['process']);
        $this->assertArrayHasKey('key-1', $result['process']);
    }

    public function test_delete_removes_from_disk_and_db(): void
    {
        Storage::fake('local');

        $user = User::create([
            'username' => 'delete-test-user',
            'password' => bcrypt('Password@123'),
        ]);
        $event = $user->events()->create([
            'title' => '删除测试',
            'status' => Event::STATUS_PROCESSED,
            'visibility' => Event::VISIBILITY_PRIVATE,
        ]);
        $record = $event->records()->create([
            'user_id' => $user->id,
            'process' => null,
            'result' => null,
        ]);

        $path = 'event-files/delete-test/delete-test.txt';
        Storage::disk('local')->put($path, 'content');

        $file = $record->files()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'usage' => EventFile::USAGE_ATTACHMENT,
            'disk' => 'local',
            'path' => $path,
            'original_name' => 'delete-test.txt',
            'mime_type' => 'text/plain',
            'size' => 7,
        ]);

        Storage::disk('local')->assertExists($path);
        $this->assertDatabaseHas('event_files', ['id' => $file->id]);

        $service = $this->app->make(EventFileService::class);
        $service->delete($file);

        Storage::disk('local')->assertMissing($path);
        $this->assertDatabaseMissing('event_files', ['id' => $file->id]);
    }

    public function test_files_handles_single_uploaded_file(): void
    {
        $fakeFile = UploadedFile::fake()->image('single.jpg');
        $request = Request::create('/', 'POST');
        $request->files->set('test_images', $fakeFile);

        $service = $this->app->make(EventFileService::class);
        $method = new \ReflectionMethod($service, 'files');
        $result = $method->invoke($service, $request, 'test_images');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(UploadedFile::class, $result[0]);
    }

    public function test_keys_filters_empty_strings(): void
    {
        $request = Request::create('/', 'POST', [
            'test_keys' => json_encode([' key-1 ', '', '  ', 'key-2']),
        ]);

        $service = $this->app->make(EventFileService::class);
        $method = new \ReflectionMethod($service, 'keys');
        $result = $method->invoke($service, $request, 'test_keys');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('key-1', $result[0]);
        $this->assertSame('key-2', $result[1]);
    }
}
