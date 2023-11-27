<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class News extends JsonResource
{
    public function toArray($request)
    {
        return [
            'category_id' => $this->category_id,
            'category_name' => $this->category->web_name,
            'source_id' => $this->source_id,
            'source_name' => $this->source->web_name,
            'author_id' => $this->author_id,
            'author_name' => $this->author->web_name,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'image' => asset('/storage').'/news/'. $this->provider->name .'/'. $this->image,
            'url' => $this->url,
            'published_at' => $this->published_at
        ];
    }
}
