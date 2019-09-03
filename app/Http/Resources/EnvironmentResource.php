<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;

class EnvironmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $dateFormat = $request->input('date_format', $this->resource->getDateFormat());

        return [
            'id' => $this->id,
            'variables' => $this->resource->variables(),
            'created_at' => Date::instance($this->{$this->resource->getCreatedAtColumn()})->format($dateFormat),
            'updated_at' => Date::instance($this->{$this->resource->getUpdatedAtColumn()})->format($dateFormat),
        ];
    }
}
