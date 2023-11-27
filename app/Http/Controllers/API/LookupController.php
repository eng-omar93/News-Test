<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Helpers\apiHelper;

use App\Models\Author;
use App\Models\Source;
use App\Models\Category;

use App\Http\Resources\Author as AuthorResource;
use App\Http\Resources\Source as SourceResource;
use App\Http\Resources\Category as CategoryResource;

use Exception;


class LookupController extends Controller
{
    public function __construct()
    {
    }

    public function authors()
    {
      try {
        $data = Author::orderBy('name')->get();
        return apiHelper::okResponse(AuthorResource::collection($data));
      } catch (\Exception $e) {
        return apiHelper::failResponse(json_encode($e->getMessage()));
      }
    }

    public function sources()
    {
      try {
        $data = Source::orderBy('name')->get();
        return apiHelper::okResponse(SourceResource::collection($data));
      } catch (\Exception $e) {
        return apiHelper::failResponse(json_encode($e->getMessage()));
      }
    }

    public function categories()
    {
      try {
        $data = Category::orderBy('name')->get();
        return apiHelper::okResponse(CategoryResource::collection($data));
      } catch (\Exception $e) {
        return apiHelper::failResponse(json_encode($e->getMessage()));
      }
    }

}
