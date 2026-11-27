<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'pelanggan'] as $nama) {
            Role::firstOrCreate(['nama_role' => $nama]);
        }
    }
}
