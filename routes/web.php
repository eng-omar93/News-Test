<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use jcobhams\NewsApi\NewsApi;
use App\Models\NewsProvider;
use App\Models\Category;
use App\Models\News;

use App\Models\NewsDataSources\NewsApiOrg;
use App\Models\NewsDataSources\TheGuardian;
use App\Models\NewsDataSources\NYTimes;

use Illuminate\Support\Facades\Http;


Route::get('/', function () {
    dd(Category::take(20)->get());
});
