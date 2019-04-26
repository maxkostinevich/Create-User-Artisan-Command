<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Validator;

class UsersCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Ask for user details
        $name = $this->ask('Name');
        $email = $this->ask('Email');
        $password = $this->secret('Password');
        $confirm_password = $this->secret('Confirm password');

        // Check if entered passwords match
        while($password != $confirm_password){
            $this->error('Passwords do not match, please try again.');

            $password = $this->secret('Password');
            $confirm_password = $this->secret('Confirm password');
        }

        // Validate input data
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:5']
        ]);

        if($validator->fails()){
            $this->info('Oops! Something went wrong. See error messages below:');
            foreach($validator->errors()->all() as $error){
                $this->error($error);
            }
            return false;
        }

        // Create new user
        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->save();
        $this->info('User '. $name .' ('. $email .') has been created!');
        return true;
    }
}
