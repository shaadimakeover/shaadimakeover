<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "post_id" => $this->id,
            "artist_avatar" => isset($this->artist->profile_photo_path) ? config('app.url') . '/' . $this->artist->profile_photo_path : "https://cdn-icons-png.flaticon.com/512/21/21104.png",
            "artist_name" => $this->artist->full_name,
            "post_attachment" => config('app.url').'/'.$this->post_attachment,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
