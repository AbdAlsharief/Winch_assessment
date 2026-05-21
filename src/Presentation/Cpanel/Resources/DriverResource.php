<?php

namespace Src\Presentation\Cpanel\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'current_location' => [
                'latitude' => (float) $this->current_latitude,
                'longitude' => (float) $this->current_longitude,
            ],
            'active_orders_count' => $this->when(
                $this->relationLoaded('orders'),
                fn() => $this->orders->count()
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
