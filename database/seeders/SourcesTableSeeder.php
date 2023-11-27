<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourcesTableSeeder extends Seeder
{

    public function run()
    {
        Source::insert(['name'=> 'unknown' ]);
    }
}
