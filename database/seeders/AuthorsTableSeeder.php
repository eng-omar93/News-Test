<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorsTableSeeder extends Seeder
{

    public function run()
    {
        Author::insert(['name'=> 'unknown' ]);
    }
}
