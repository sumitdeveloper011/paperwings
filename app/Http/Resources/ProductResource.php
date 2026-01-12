<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (float) $this->total_price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'effective_price' => (float) ($this->discount_price ?? $this->total_price),
            'image' => $this->when(
                $this->relationLoaded('images') && $this->images->isNotEmpty(),
                fn() => $this->images->first()->image_url,
                asset('assets/images/placeholder.jpg')
            ),
            'category' => $this->whenLoaded('category', function() {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'url' => route('product.detail', $this->slug),
            'in_stock' => $this->when(isset($this->stock), $this->stock > 0),
        ];
    }
}

