<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use mysql_xdevapi\Table;
use Psy\Util\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
$this->call([
    UserSeeder::class,
]);

    }
}
