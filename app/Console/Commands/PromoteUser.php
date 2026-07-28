<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUser extends Command
{
    protected $signature = 'user:promote {email : Email del usuario a promover} {role=admin : Rol a asignar (admin|mecanico)}';
    protected $description = 'Promover un usuario a un rol específico (admin por defecto)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        if (!in_array($role, ['admin', 'mecanico'])) {
            $this->error('Rol inválido. Use: admin o mecanico');
            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario con email '{$email}' no encontrado.");
            return self::FAILURE;
        }

        $oldRole = $user->role;
        $user->role = $role;
        $user->save();

        $this->info("Usuario '{$user->name}' promovido de '{$oldRole}' a '{$role}'.");
        return self::SUCCESS;
    }
}
