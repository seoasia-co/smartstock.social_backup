<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PunbotProductEcommerce extends Model 
{

    use HasFactory;

    protected $connection='punbot_db';
    protected $table = 'ecommerce_product';
    protected $fillable = [
    
        'user_id' ,
        'store_id' ,
        'product_name' ,
        'cf_code' ,
        'product_description' ,
        'product_video_id' ,
        'original_price' ,
        'sell_price' ,
        'taxable' ,
        'stock_item' ,
        'stock_display' ,
        'stock_prevent_purchase' ,
        'attribute_ids' ,
        'preparation_time' ,
        'preparation_time_unit' ,
        'purchase_note' ,
        'thumbnail' ,
        'featured_images' ,
        'digital_product_file' ,
        'category_id' ,
        'sales_count' ,
        'visit_count' ,
        'updated_at' ,
        'status' ,
        'deleted' ,
        'woocommerce_product_id' ,
        'woocommerce_price_html' ,
        'related_product_ids' ,
        'upsell_product_id' ,
        'downsell_product_id' ,
        'is_featured' ,
            
     
    ];

}