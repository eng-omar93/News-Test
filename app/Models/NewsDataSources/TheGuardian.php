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

use Guardian\GuardianAPI;

class TheGuardian extends Base
{

    public function apiDataToNewsArray(bool $isRepeatedForCategories = false, bool $isRepeatedForResources = false, bool $isRepeatedForAuthers = false)
    {

        try {

            $data = [];
            $token = $this->token;
            $newsapi = new GuardianAPI($token);

            try {
                if ($isRepeatedForCategories)
                {
                    foreach ($this->categoriesArray as $categoryId => $categoryName)
                    {
                        try
                        {


                            $response = $newsapi->content()
                                                ->setSection($categoryName)
                                                ->setShowFields('byline,publication,bodyText,firstPublicationDate,thumbnail')
                                                ->setFromDate(new \DateTimeImmutable(date('Y-m-d H:i:s',$this->getLatestPublishDateByProvider()))) //init in __construct so it will not changed in categories
                                                ->setOrderBy("oldest") // so in next run i can get new articles
                                                ->setPageSize(1)
                                                ->fetch();

                            $this->parseResponse($response->response, $categoryName, $data);

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
                        $response = $newsapi->content()
                                            ->setSection('news')
                                            ->setShowFields('byline,publication,bodyText,firstPublicationDate,thumbnail')
                                            ->setFromDate(new \DateTimeImmutable(date('Y-m-d H:i:s',$this->getLatestPublishDateByProvider()))) //init in __construct so it will not changed in categories
                                            ->setOrderBy("oldest") // so in next run i can get new articles
                                            ->setPageSize(25)
                                            ->fetch();
                        $this->parseResponse($response->response, 'news', $data);
                        dump(array_values($data));
                    } catch (\Exception $e)
                    {
                        print(json_encode(array('msg'=>$e->getMessage())) . " \n");
                        $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                    }
                }
                $this->log(NULL, NULL, 1, NULL);
                $this->setNewsArray(array_values($data));

            } catch (\Exception $e)
            {
                print(json_encode(array('msg'=>$e->getMessage())) . " \n");
                $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                return;
            }

            print("ok apiDataToNewsArray \n");
            return true;

        } catch (\Exception $e)
        {
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");
            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
            print("error in apiDataToNewsArray \n");
            return false;
        }
    }

    public function parseResponse($response, $categoryName, & $data)
    {
        try {

            if (isset($response->status) && isset($response->total))
            {
                if ($response->status == 'ok' && $response->total > 0)
                {
                    $articles = $response->results;
                    foreach ($articles as $key => $article)
                    {
                        $tempNew = [];
                        $tempNew['provider_id'] = $this->getNewsProvider()->id;
                        $tempNew['category_id'] = $categoryName;
                        $tempNew['source_id'] = $article->fields->publication ?? 'unknown';
                        $tempNew['author_id'] = $article->fields->byline ?? 'unknown';
                        $tempNew['title'] = $article->webTitle ?? '';
                        $tempNew['description'] = '';
                        $tempNew['content'] = $article->fields->bodyText ?? '';
                        $tempNew['url'] = $article->webUrl ?? '';
                        $tempNew['image'] = $this->saveImage($article->fields->thumbnail ?? '', $key) ?? '';
                        $tempNew['published_at'] = date('Y-m-d H:i:s', strtotime($article->fields->firstPublicationDate ?? now()));
                        array_push($data, $tempNew);
                    }
                }else{
                    $this->log(NULL, NULL, 0, 'api failure');
                }
            }

        } catch (\Exception $e) {
            // dd($e);
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
            // dd($e);
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");
            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
            print("error in saveImage \n");
            return '';
        }

    }


}
