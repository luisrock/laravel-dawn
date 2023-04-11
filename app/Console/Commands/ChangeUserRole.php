<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ChangeUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-role {user_id} {role_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change role of a user using their ID and role name';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $roleName = $this->argument('role_name');

        // Find the user by ID
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        // Check if the role exists
        $role = Role::findByName($roleName);

        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return 1;
        }

        // Remove existing roles and assign the new role
        $user->syncRoles($roleName);

        $this->info("Role '{$roleName}' has been assigned to user '{$user->name}' with ID {$userId}.");
        return 0;
    }
}