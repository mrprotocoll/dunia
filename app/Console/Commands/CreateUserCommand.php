<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $user['name'] = $this->ask('Name of the user');
        $user['email'] = $this->ask('Email of the user');
        $user['password'] = $this->secret('Password of the user');
        $user['role'] = 'admin';

        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return -1;
        }

        $user['password'] = Hash::make($user['password']);
        User::create($user);

        $this->info('User '.$user['email'].' created successfully');

        return 0;
    }
}
