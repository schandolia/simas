<?php

use App\Model\DocType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesSeeder::class);
        $this->call(RightsSeeder::class);
        $this->call(RoleRightsSeeder::class);
        // $this->call(UserSeeder::class);
        $this->call(DocTypeSeeder::class);

    }
}
