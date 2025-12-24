<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? '',
            'product_slug' => $this->product->slug ?? '',
            'product_image' => $this->when(
                $this->relationLoaded('product') && 
                $this->product && 
                $this->product->relationLoaded('images') && 
                $this->product->images->isNotEmpty(),
                fn() => $this->product->images->first()->image_url,
                asset('assets/images/placeholder.jpg')
            ),
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'subtotal' => (float) $this->subtotal,
            'product_url' => $this->product ? route('product.detail', $this->product->slug) : '#',
        ];
    }
}

