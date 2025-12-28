<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistItemResource extends JsonResource
{
    // Transform the resource into an array
    public function toArray(Request $request): array
    {
        $product = $this->product;
        
        if (!$product) {
            return [
                'id' => $this->id,
                'product_id' => $this->product_id,
                'error' => 'Product not found',
            ];
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'product_price' => (float) $product->total_price,
            'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
            'product_image' => $this->when(
                $product->relationLoaded('images') && $product->images->isNotEmpty(),
                fn() => $product->images->first()->image_url,
                asset('assets/images/placeholder.jpg')
            ),
            'product_url' => route('product.detail', $product->slug),
        ];
    }
}

