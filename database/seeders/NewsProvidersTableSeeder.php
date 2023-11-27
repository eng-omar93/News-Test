<?php

namespace Database\Seeders;

use App\Models\NewsProvider;
use Illuminate\Database\Seeder;

class NewsProvidersTableSeeder extends Seeder
{

    public function run()
    {
        NewsProvider::insert([
            ['name' => 'newsapi.org', 'token' => '0edc0d4f92d342b8bc8eb9b1b8193ad7', 'has_published_at' => 0],
            ['name' => 'theguardian', 'token' => '6ee92b96-2040-4a53-a357-df77751e2c53', 'has_published_at' => 1],
            ['name' => 'nytimes', 'token' => 'PcrSTIt0nGK01ZimJls75EkmpsxrPUzM', 'has_published_at' => 1]
        ]);
    }
}
