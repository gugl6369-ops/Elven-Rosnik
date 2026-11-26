<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EnsureAdminCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'elfi:ensure-admin';

    /**
     * @var string
     */
    protected $description = 'Create or update the admin user from ADMIN_USERNAME, ADMIN_EMAIL, and ADMIN_PASSWORD';

    public function handle(): int
    {
        $username = $this->envString('ADMIN_USERNAME');
        $email = $this->envString('ADMIN_EMAIL');
        $password = $this->envString('ADMIN_PASSWORD');

        if ($username === null) {
            $this->error('ADMIN_USERNAME must be set in the environment.');

            return self::FAILURE;
        }

        if ($email === null) {
            $this->error('ADMIN_EMAIL must be set in the environment.');

            return self::FAILURE;
        }

        if ($password === null) {
            $this->error('ADMIN_PASSWORD must be set in the environment.');

            return self::FAILURE;
        }

        $user = User::query()->where('username', $username)->first();

        if ($user === null) {
            $user = User::query()->where('email', $email)->first();
        }

        $attributes = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'is_admin' => true,
        ];

        if ($user !== null) {
            $user->fill($attributes);
            $user->save();
            $this->info("Admin user updated (username: {$username}).");

            return self::SUCCESS;
        }

        User::query()->create($attributes);
        $this->info("Admin user created (username: {$username}).");

        return self::SUCCESS;
    }

    private function envString(string $key): ?string
    {
        $value = env($key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }
}