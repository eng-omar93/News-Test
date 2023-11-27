<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Helpers\apiHelper;


use App\Models\Author;
use App\Models\Source;
use App\Models\Category;
use App\Models\News;

use App\Http\Resources\Author as AuthorResource;
use App\Http\Resources\Source as SourceResource;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\News as NewsResource;

use Exception;


class NewsController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {

        try {

            // TODO: params validations
            $q = $request->q ?? '';
            $date_from = $request->date_from ?? 'all';
            $date_to = $request->date_to ?? 'all';
            $category = $request->category ?? 'all';
            $source = $request->source ?? 'all';
            $page = $request->page ?? 1;
            $per_page =20;

            if (isset($request->per_page))
            if ( (int) $request->per_page <= 100 )
            $per_page = $request->per_page;


            $newsQuery = News::with('author','category','source', 'provider');

            if ($date_from != 'all')
            {
                $date_from = $date_from . ' 00:00:01';
                $newsQuery = $newsQuery->where('published_at', '>=' , $date_from);
            }

            if ($date_to != 'all')
            {
                $date_to = $date_to . ' 23:59:59';
                $newsQuery = $newsQuery->where('published_at', '<=' , $date_to);
            }

            if ($category != 'all')
            {
                $newsQuery = $newsQuery->where('category_id', $category);
            }

            if ($source != 'all')
            {
                $newsQuery = $newsQuery->where('source_id', $source);
            }

            if ($q != '')
            {
                $words = explode(' ', $q);
                if (count($words)) {
                    foreach ($words as $w) {
                        if (strlen($w) > 2) {
                            $newsQuery = $newsQuery->where(function($qq)use($words, $w) {
                                $qq->where('id' , '0');
                                        $qq->orWhere('title', 'like', '%'.$w.'%');
                                        $qq->orWhere('description', 'like', '%'.$w.'%');
                                        $qq->orWhere('content', 'like', '%'.$w.'%');
                            });
                        }
                    }
                }
            }

            $data = $newsQuery->orderBy('published_at','desc')->paginate($per_page, ['*'], 'page', $page);

            $data = json_decode(json_encode($data)); // so we can access all the protected property
            // dd($data);

            return apiHelper::okResponse([
                    'total' => $data->total,
                    'current_page' => $data->current_page,
                    'last_page' => $data->last_page,
                    'per_page' => $data->per_page,
                    'news' => NewsResource::collection($data->data),
                ]);

        } catch (\Exception $e) {
            // dd($e);
            return apiHelper::failResponse(json_encode($e->getMessage()));
        }


    }



    public function userPreferences(Request $request)
    {
        try {

            // TODO: params validations
            $q = $request->q ?? '';
            $date_from = $request->date_from ?? 'all';
            $date_to = $request->date_to ?? 'all';
            $categories = array_filter(  explode('_',$request->categories) , 'strlen' ) ?? [];
            $sources = array_filter(  explode('_',$request->sources) , 'strlen' ) ?? [];
            $authors = array_filter(  explode('_',$request->authors) , 'strlen' ) ?? [];
            $page = $request->page ?? 1;
            $per_page =20;

            if (isset($request->per_page))
            if ( (int) $request->per_page <= 100 )
            $per_page = $request->per_page;


            $newsQuery = News::with('author','category','source', 'provider');

            if ($date_from != 'all')
            {
                $date_from = $date_from . ' 00:00:01';
                $newsQuery = $newsQuery->where('published_at', '>=' , $date_from);
            }

            if ($date_to != 'all')
            {
                $date_to = $date_to . ' 23:59:59';
                $newsQuery = $newsQuery->where('published_at', '<=' , $date_to);
            }

            if (count($categories) > 0)
            {
                $newsQuery = $newsQuery->orWhereIn('category_id', $categories);
            }

            if (count($sources) > 0)
            {
                $newsQuery = $newsQuery->orWhereIn('source_id', $sources);
            }

            if (count($authors) > 0)
            {
                $newsQuery = $newsQuery->orWhereIn('author_id', $authors);
            }

            if ($q != '')
            {
                $words = explode(' ', $q);
                if (count($words)) {
                    foreach ($words as $w) {
                        if (strlen($w) > 2) {
                            $newsQuery = $newsQuery->where(function($qq)use($words, $w) {
                                $qq->orWhere('title', 'like', '%'.$w.'%');
                                $qq->orWhere('description', 'like', '%'.$w.'%');
                                $qq->orWhere('content', 'like', '%'.$w.'%');
                            });
                        }
                    }
                }
            }

            $data = $newsQuery->orderBy('published_at','desc')->paginate($per_page, ['*'], 'page', $page);

            $data = json_decode(json_encode($data)); // so we can access all the protected property
            // dd($data);

            return apiHelper::okResponse([
                    'total' => $data->total,
                    'current_page' => $data->current_page,
                    'last_page' => $data->last_page,
                    'per_page' => $data->per_page,
                    'news' => NewsResource::collection($data->data),
                ]);

        } catch (\Exception $e) {
            // dd($e);
            return apiHelper::failResponse(json_encode($e->getMessage()));
        }
    }
}
