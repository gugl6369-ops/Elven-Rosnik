<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContinueRegistrationAvatarTest extends TestCase
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

    public function test_continue_registration_accepts_avatar_upload(): void
    {
        $email = 'avatar-test-'.uniqid().'@example.com';
        $username = 'user_'.substr(uniqid(), -8);

        $response = $this->post('/register/continue', [
            'email' => $email,
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'username' => $username,
            'name' => 'Avatar Tester',
            'status' => 'online',
            'avatar' => $this->tinyPngUploadedFile(),
        ], [
            'Accept' => 'text/html, application/xhtml+xml',
        ]);

        $response->assertRedirect(route('home'));

        $user = User::query()->where('email', $email)->first();

        $this->assertNotNull($user);
        $this->assertNotNull($user->avatar);
        $this->assertFileExists(public_path(ltrim($user->avatar, '/')));
    }
}