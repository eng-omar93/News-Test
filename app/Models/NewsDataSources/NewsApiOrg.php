<?php
namespace App\Models\NewsDataSources;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


use App\Models\News;
use App\Models\Category;
use App\Models\Auther;
use App\Models\Source;
use App\Models\JobLog;

use jcobhams\NewsApi\NewsApi;

class NewsApiOrg extends Base
{

    public function apiDataToNewsArray(bool $isRepeatedForCategories = false, bool $isRepeatedForResources = false, bool $isRepeatedForAuthers = false)
    {
        try {

            $data = [];
            $token = $this->token;
            $newsapi = new NewsApi($token);

            try {
                if ($isRepeatedForCategories)
                {
                    foreach ($this->categoriesArray as $categoryId => $categoryName)
                    {
                        try
                        {
                            $response = $newsapi->getTopHeadlines(null,null,'us',$categoryName,10,1) ?? [];
                            $this->parseResponse($response, $categoryName, $data);

                        } catch (\Exception $e)
                        {
                            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

                            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                            continue;
                        }
                    }
                }else
                {
                    try {
                        $response = $newsapi->getTopHeadlines(null,null,'us','general',100,1) ?? [];
                        $this->parseResponse($response, 'general', $data);
                    } catch (\Exception $e)
                    {
                        $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                    }
                }
                $this->log(NULL, NULL, 1, NULL);
                $this->setNewsArray(array_values($data));

            } catch (\Exception $e)
            {
                $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                return;
            }

            print("ok apiDataToNewsArray \n");
            return true;

        } catch (\Exception $e)
        {
            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");
            print("error in apiDataToNewsArray \n");
            return false;
        }
    }

    public function parseResponse($response, $categoryName, & $data)
    {
        try {

            if (isset($response->status) && isset($response->totalResults))
            {
                if ($response->status == 'ok' && $response->totalResults > 0)
                {
                    $articles = $response->articles;
                    foreach ($articles as $key => $article)
                    {
                        if ($article->title == '[Removed]' || $article->source->name == '[Removed]') {
                            continue;
                        }
                        $tempNew = [];
                        $tempNew['provider_id'] = $this->getNewsProvider()->id;
                        $tempNew['category_id'] = $categoryName;
                        $tempNew['source_id'] = $article->source->name ?? 'unknown';
                        $tempNew['author_id'] = $article->author ?? 'unknown';
                        $tempNew['title'] = $article->title ?? '';
                        $tempNew['description'] = $article->description ?? '';
                        $tempNew['content'] = $article->content ?? '';
                        $tempNew['url'] = $article->url ?? '';
                        $tempNew['image'] = $this->saveImage($article->urlToImage ?? '', $key) ?? '';
                        $tempNew['published_at'] = date('Y-m-d H:i:s', strtotime($article->publishedAt ?? now()));

                        array_push($data, $tempNew);
                    }
                }else{
                    $this->log(NULL, NULL, 0, 'api failure');
                }
            }
        } catch (\Exception $e) {
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
            print("error in parseResponse \n");
            return false;
        }

    }

    public function saveImage(string $imageURL, string $id)
    {
        try {
            if ($imageURL == '') {
                return '';
            }
            $imageName = 'main_image_' . $id . '_' . date('Ymdhis'). '.jpg';
            $path = 'news/'. $this->getNewsProvider()->name .'/'. $imageName;
            Storage::disk('public')->put( $path, file_get_contents($imageURL));
            return $imageName;
        } catch (\Exception $e) {
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
            print("error in saveImage \n");
            return false;
        }

    }

}
