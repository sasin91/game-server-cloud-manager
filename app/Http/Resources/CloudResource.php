<?php

namespace App\Http\Resources;

use App\Http\Resources\TeamResource;
use Illuminate\Support\Facades\Date;
use App\Http\Resources\EnvironmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CloudResource extends JsonResource
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
            'model_id' => $this->model_id,
            'environment_id' => $this->environment_id,
            'provider' => $this->provider,
            'private_network' => $this->private_network,
            'address' => $this->address,
            'created_at' => Date::instance($this->{$this->resource->getCreatedAtColumn()})->format($dateFormat),
            'updated_at' => Date::instance($this->{$this->resource->getUpdatedAtColumn()})->format($dateFormat),
            'deleted_at' => $this->when($this->resource->trashed(), function () use ($dateFormat) {
                return Date::instance($this->{$this->resource->getDeletedAtColumn()})->format($dateFormat);
            }),

            'model' => $this->whenLoaded('model', function () {
                return new TeamResource(
                    $this->model
                );
            }),

            'environment' => $this->whenLoaded('environment', function () {
                return new EnvironmentResource(
                    $this->environment
                );
            })
        ];
    }
}
