<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsProvider;

use App\Models\NewsDataSources\NewsApiOrg;
use App\Models\NewsDataSources\TheGuardian;
use App\Models\NewsDataSources\NYTimes;

class ImportNews extends Command
{
    /**
     * @var string
     */
    // protected $signature = 'import:products';
    protected $signature = 'import:news';


    /**
     * @var string
     */
    protected $description = 'Imports news into database';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function handle()
    {

        $sourcesProviders = [
            new NewsApiOrg(NewsProvider::find(1),true,false,false),
            new TheGuardian(NewsProvider::find(2),true,false,false),
            new NYTimes(NewsProvider::find(3),true,false,false),
        ];

        foreach ($sourcesProviders as $key => $newsProvider)
        {
            try {
                print("hi \n");
                $newsProvider->run();
            } catch (\Exception $e) {
                continue;
            }
        };

        return;
    }
}
