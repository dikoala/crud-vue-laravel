<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // default setup would return all
        //return parent::toArray($request);

        // custom
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'slug' => $this->slug,
            'tags' => $this->tags,
            'photo' => $this->cover_photo,
            'created' => date("F j, Y, g:i a", strtotime($this->created_at)),
            'updated' => date("F j, Y, g:i a", strtotime($this->updated_at))
        ];
    }
}
