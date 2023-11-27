<?php
namespace App\Models\NewsDataSources;



use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\News;
use App\Models\NewsProvider;
use App\Models\Category;
use App\Models\Author;
use App\Models\Source;
use App\Models\JobLog;


abstract class Base implements DataSourceInterface
{

    protected $newsProvider;
    protected $token;
    protected $categoriesArray;
    protected $categoriesMap; // ['name' => 'id']
    protected $authorsArray;
    protected $authorsMap;    // ['name' => 'id']
    protected $sourcesArray;
    protected $sourcesMap;    // ['name' => 'id']
    protected $repeatedForCategories;
    protected $repeatedForResources;
    protected $repeatedForAuthors;
    protected $latestPublishDateByProvider;
    protected $newsArray;

    abstract public function apiDataToNewsArray(bool $isRepeatedForCategories = false, bool $isRepeatedForResources = false, bool $isRepeatedForAuthors = false);
    abstract public function saveImage(string $imageURL, string $id);

    public function __construct(
                                    NewsProvider $newsProvider,
                                    bool $isRepeatedForCategories = false,
                                    bool $isRepeatedForResources = false,
                                    bool $isRepeatedForAuthors = false
                                )
    {

        $this->newsProvider = $newsProvider;
        $this->token = $newsProvider->token ?? ''; //it may change by setToken method
        $this->repeatedForCategories = $isRepeatedForCategories;
        $this->repeatedForResources = $isRepeatedForResources;
        $this->repeatedForAuthors = $isRepeatedForAuthors;


        $categories = Category::pluck('name','id')->toArray() ?? [];
        $this->categoriesArray = $categories;
        $this->categoriesMap = array_flip($categories) ?? [];

        $sources = Source::pluck('name','id')->toArray() ?? [];
        $this->sourcesArray = $sources;
        $this->sourcesMap = array_flip($sources) ?? [];

        $authors = Author::pluck('name','id')->toArray() ?? [];
        $this->authorsArray = $authors;
        $this->authorsMap = array_flip($authors) ?? [];

        $this->latestPublishDateByProvider = strtotime( $newsProvider->news()->max('published_at') ?? '-1 days' );

    }

    public function run()
    {
        try
        {
            ini_set('max_execution_time', 3600); //60 minutes

            $this->apiDataToNewsArray($this->repeatedForCategories, $this->repeatedForResources, $this->repeatedForAuthors);
            return $this->insertNews();

        } catch (\Exception $e)
        {
            // dd($e);
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

            return false;
        }
    }

    public function insertNews()
    {
        try
        {
            $newsArr = $this->getNewsArray();
            foreach ($newsArr as $key => $n)
            {
                $n['category_id'] = $this->crateOrGetCategoryId($n['category_id']);
                $n['author_id'] = $this->crateOrGetAuthorId($n['author_id']);
                $n['source_id'] = $this->crateOrGetSourceId($n['source_id']);
                // if ($this->newsProvider->has_published_at == 0)
                // {
                    try {
                        News::firstOrCreate(['title' =>  $n['title'], 'author_id' => $n['author_id'], 'category_id' => $n['category_id']], $n);
                    } catch (\Exception $e) {
                        continue;
                    }
                // }
            }

            // if ($this->newsProvider->has_published_at == 1)
            // {
                // News::insert($newsArr);
            // }

            return true;
        }
        catch (\Exception $e)
        {
            // dd($e);
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

            print("error in Base insertNews \n");
            return false;
        }
    }

    public function crateOrGetCategoryId(string $categoryName = '')
    {
        return $this->categoriesMap[$categoryName] ?? ( ( Category::firstOrCreate(['name' =>  $categoryName],['name' =>  $categoryName]) )->id ?? 1);
    }

    public function crateOrGetAuthorId(string $authorName = '')
    {
        return $this->authorsMap[$authorName] ?? ( ( Author::firstOrCreate(['name' =>  $authorName]) )->id ?? 1);
    }

    public function crateOrGetSourceId(string $sourceName = '')
    {
        return $this->sourcesMap[$sourceName] ?? ( ( Source::firstOrCreate(['name' =>  $sourceName]) )->id ?? 1);
    }

    public function setToken(string $val = '')
    {
        $this->token = $val;
        return;
    }
    public function getToken()
    {
        return $this->token;
    }

    public function setNewsArray(array $val)
    {
        $this->newsArray = $val;
        return;
    }

    public function getNewsArray()
    {
        return $this->newsArray;
    }

    public function setLatestPublishDateByProvider(string $val = '')
    {
        $this->latestPublishDateByProvider = $val;
        return;
    }

    public function getLatestPublishDateByProvider()
    {
        return $this->latestPublishDateByProvider;
    }

    public function getNewsProvider()
    {
        return $this->newsProvider;
    }


    public function log($request = '', $response = '', $status = 1, $error = '')
    {
        try {
            return JobLog::create([
                        'provider_id' => $this->newsProvider->id,
                        'status' => $status,
                        'request' => json_encode($request),
                        'responce' => json_encode($response),
                        'error' => json_encode($error)
                    ]);
        } catch (\Exception $e) {
            // dd($e);
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

            print("error in log \n");
            return false;
        }
    }

}
