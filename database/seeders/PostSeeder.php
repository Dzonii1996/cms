<?php

namespace Database\Seeders;

use App\Models\PostRelation;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $langs = Config::get('languages');
        $authors = User::all();
        $statuses = Status::all();

        for ($i=0; $i <= 10; $i++) {
            $title = 'Title '.$i;

            $main_post_id = PostRelation::create();

            foreach ($langs as $lang){
                DB::table('posts')->insert(
                    [
                        'main_post_id' => $main_post_id->id,
                        'lang' => $lang,
                        'title' => 'Title '.$i.' '.$lang,
                        'slug' => Str::slug($title.$lang),
                        'content' => 'lorem ipsum',
                        'author_id' => $authors->random()->id,
                        'status_id' => $statuses->random()->id,
                    ]
                );
            }
        }
    }
}
