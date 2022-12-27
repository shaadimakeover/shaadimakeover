<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $model_image = Media::where(['model_id' => $this->id, 'collection_name' => 'category'])->first();

        return [
            "cat_id" => $this->id,
            "cat_title" => $this->title,
            "thumbnail" => "https://www.makeupwale.com/wp-content/uploads/2021/07/Aditi-Kumari-Bridal-Makeup-Artist-in-Kolkata-West-Bengal-Profile-Pic.jpg",
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y'),
        ];
    }
}
