<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ArticleResource extends JsonResource
{

    public function toArray($request)
    {
       
        return [
            'title'        => $this->title ?? null,
            'description'  => $this->description ?? null,
            'content'      => $this->content ?? null,
            'url'          => $this->url ?? null,
            'image_url'    => $this->image_url ?? null,
            'published_at' => $this->published_at instanceof Carbon
                                ? $this->published_at->toIso8601String()
                                : Carbon::parse($this->published_at)->toIso8601String(),
            'source_name'  => $this->source_name ?? null,
            'author'       => $this->author ?? null,
            'category'     => $this->category ?? 'general',
        ];
    }
}
