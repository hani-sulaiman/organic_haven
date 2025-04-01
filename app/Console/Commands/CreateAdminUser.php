<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Admin User Creation');

        // Prompt for user inputs
        $name = $this->ask('Name');
        $email = $this->ask('Email');
        $password = $this->secret('Password');
        $password_confirmation = $this->secret('Confirm Password');
        $birthdate = $this->ask('Birthdate (YYYY-MM-DD)', null);
        $gender = $this->choice('Gender', ['male', 'female', 'other'], 2);

        // Validate inputs
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
            'birthdate' => $birthdate,
            'gender' => $gender,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'birthdate' => 'nullable|date|before:today',
            'gender' => 'required|in:male,female,other',
        ]);

        if ($validator->fails()) {
            $this->error('Validation errors occurred:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("- $error");
            }
            return 1; // Indicate failure
        }

        // Create the admin user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'role' => 'admin',
                'birthdate' => $birthdate,
                'gender' => $gender,
                'password' => Hash::make($password),
                // If you want to automatically verify the admin's email:
                'email_verified_at' => now(),
                // Optionally, generate and set OTP if needed
                // 'otp_code' => Str::random(6),
                // 'otp_expires_at' => now()->addMinutes(10),
            ]);

            $this->info("Admin user '{$user->name}' created successfully with email '{$user->email}'.");
            return 0; // Indicate success
        } catch (\Exception $e) {
            $this->error('An error occurred while creating the admin user: ' . $e->getMessage());
            return 1; // Indicate failure
        }
    }
}
