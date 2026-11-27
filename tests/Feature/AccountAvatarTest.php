<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountAvatarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->schemaReady()) {
            $this->markTestSkipped('Database schema not available in test environment.');
        }
    }

    private function schemaReady(): bool
    {
        try {
            return DB::table('user')->limit(1)->exists()
                || DB::table('user')->limit(1)->doesntExist();
        } catch (\Throwable) {
            return false;
        }
    }

    private function createUser(): User
    {
        $suffix = substr(uniqid(), -8);

        return User::query()->create([
            'username' => 'avatar_'.$suffix,
            'email' => 'avatar_'.$suffix.'@example.com',
            'password' => 'Password1!',
            'name' => 'Avatar Tester',
            'status' => 'online',
            'is_admin' => false,
        ]);
    }

    private function tinyPngUploadedFile(string $name = 'avatar.png'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'avatar_');
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
            true
        );

        file_put_contents($path, $png);

        return new UploadedFile($path, $name, 'image/png', \UPLOAD_ERR_OK, true);
    }

    public function test_authenticated_user_can_update_avatar(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/account/avatar', [
            'avatar' => $this->tinyPngUploadedFile(),
        ], [
            'Accept' => 'text/html, application/xhtml+xml',
        ]);

        $response->assertRedirect(route('account'));

        $user->refresh();

        $this->assertNotNull($user->avatar);
        $this->assertFileExists(public_path(ltrim($user->avatar, '/')));
    }

    public function test_avatar_update_replaces_previous_file(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->post('/account/avatar', [
            'avatar' => $this->tinyPngUploadedFile('first.png'),
        ]);

        $user->refresh();
        $firstPath = public_path(ltrim((string) $user->avatar, '/'));
        $this->assertFileExists($firstPath);

        $this->actingAs($user)->post('/account/avatar', [
            'avatar' => $this->tinyPngUploadedFile('second.png'),
        ]);

        $user->refresh();
        $secondPath = public_path(ltrim((string) $user->avatar, '/'));
        $this->assertFileDoesNotExist($firstPath);
        $this->assertFileExists($secondPath);
        $this->assertSame($firstPath, $secondPath);
    }

    public function test_guest_cannot_update_avatar(): void
    {
        $response = $this->post('/account/avatar', [
            'avatar' => $this->tinyPngUploadedFile(),
        ]);

        $response->assertRedirect('/login');
    }
}
