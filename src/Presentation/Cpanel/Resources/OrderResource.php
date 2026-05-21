<?php

namespace Src\Presentation\Cpanel\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'pickup_location' => [
                'latitude' => (float) $this->pickup_latitude,
                'longitude' => (float) $this->pickup_longitude,
            ],
            'driver' => $this->when($this->relationLoaded('driver'), function () {
                return [
                    'id' => $this->driver->id,
                    'name' => $this->driver->name,
                    'status' => $this->driver->status->value,
                    'current_location' => [
                        'latitude' => (float) $this->driver->current_latitude,
                        'longitude' => (float) $this->driver->current_longitude,
                    ],
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
