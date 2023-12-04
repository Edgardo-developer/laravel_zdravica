<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadToAmo extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'  => $request->name,
            'price' => $request->price,
            'status_id' => $request->status_id,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
        ];
    }
}
