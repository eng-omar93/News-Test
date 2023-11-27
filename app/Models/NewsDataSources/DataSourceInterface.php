<?php
namespace App\Models\NewsDataSources;


use App\Models\NewsProvider;


interface DataSourceInterface
{
    public function __construct(NewsProvider $newsProvider, bool $isRepeatedForCategories, bool $isRepeatedForResources, bool $isRepeatedForAuthors);
    public function run();
    // public function getCategories(); just for newsapi.org
    public function crateOrGetCategoryId(string $categoryName);
    public function crateOrGetAuthorId(string $authorName);
    public function crateOrGetSourceId(string $sourceName);

    public function apiDataToNewsArray(bool $isRepeatedForCategories, bool $isRepeatedForResources, bool $isRepeatedForAuthors);
    public function insertNews();
    public function saveImage(string $imageURL, string $id);
    public function log($request,$responce);

    public function setToken(string $token);
    public function getToken();
    public function setNewsArray(array $newsArr);
    public function getNewsArray();
    public function setLatestPublishDateByProvider(string $date);
    public function getLatestPublishDateByProvider();
    public function getNewsProvider();
}
