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


class NYTimes extends Base
{

    public function apiDataToNewsArray(bool $isRepeatedForCategories = false, bool $isRepeatedForResources = false, bool $isRepeatedForAuthers = false)
    {

        try {

            $data = [];
            $token = $this->token;

            try {

                if ($isRepeatedForCategories)
                {
                    foreach ($this->categoriesArray as $categoryId => $categoryName)
                    {
                        try
                        {

                            $url = 'https://api.nytimes.com/svc/topstories/v2/'.$categoryName.'.json?api-key='.$token;
                            $headers = [
                                'Accept' => 'application/json'
                            ];
                            $response = Http::withHeaders($headers)->get($url);
                            if ($response->successful())
                            {
                                $fullData = $response->object();
                                $this->parseResponse($fullData, $categoryName, $data);
                            }
                            else {
                                $this->log($url, $response, 0, '');
                            }

                        } catch (\Exception $e)
                        {
                            // dd($e);
                            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

                            print("error in apiDataToNewsArray 1 \n");
                            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                            continue;
                        }
                    }
                }else
                {
                    try {
                        $url = 'https://api.nytimes.com/svc/topstories/v2/home.json?api-key='.$token;
                        $headers = [
                            'Accept' => 'application/json'
                        ];
                        $response = Http::withHeaders($headers)->get($url);
                        if ($response->successful())
                        {
                            $fullData = $response->object();
                            // dd($fullData);
                            $this->parseResponse($fullData, 'home', $data);
                        }
                        else {
                            $this->log($url, $response, 0, '');
                        }

                    } catch (\Exception $e)
                    {
                        // dd($e);
                        print(json_encode(array('msg'=>$e->getMessage())) . " \n");

                        print("error in apiDataToNewsArray 2 \n");

                        $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                    }
                }
                $this->setNewsArray(array_values($data));

            } catch (\Exception $e)
            {
                // dd($e);
                print(json_encode(array('msg'=>$e->getMessage())) . " \n");

                print("error in apiDataToNewsArray 3 \n");

                $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
                return;
            }

            print("ok apiDataToNewsArray \n");
            return true;

        } catch (\Exception $e)
        {
            // dd($e);
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
            print("error in apiDataToNewsArray 4 \n");
            return false;
        }
    }

    public function parseResponse($response, $categoryName, & $data)
    {
        try {

            if (isset($response->status) && isset($response->num_results))
            {
                if ($response->status == 'OK' && $response->num_results > 0)
                {
                    $articles = $response->results;
                    foreach ($articles as $key => $article)
                    {
                        $tempNew = [];
                        $tempNew['provider_id'] = $this->getNewsProvider()->id;
                        $tempNew['category_id'] = $categoryName;
                        $tempNew['source_id'] = 'NY Times';
                        $tempNew['author_id'] = $article->byline ?? 'unknown';
                        $tempNew['title'] = $article->webTitle ?? '';
                        $tempNew['description'] = $article->abstract ?? '';
                        $tempNew['content'] = '';
                        $tempNew['url'] = $article->url ?? '';
                        $tempNew['image'] = $this->saveImage($article->multimedia[0]->url ?? '', $key) ?? '';
                        $tempNew['published_at'] = date('Y-m-d H:i:s', strtotime($article->published_date ?? now()));
                        array_push($data, $tempNew);
                    }
                    return;
                }else{
                    // dd($e);
                    print(json_encode(array('msg'=>$e->getMessage())) . " \n");

                    print("error in parseResponse 1 \n");
                    $this->log(NULL, NULL, 0, 'api failure');
                    return false;
                }
            }

        } catch (\Exception $e) {
            // dd($e);
            print(json_encode(array('msg'=>$e->getMessage())) . " \n");

            $this->log(NULL, NULL, 0, json_encode(array('msg'=>$e->getMessage())));
            print("error in parseResponse 2 \n");
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
