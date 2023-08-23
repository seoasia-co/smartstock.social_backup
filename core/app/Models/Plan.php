<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table='plans';
    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_users',
        'max_words',
        'max_images',
        'max_minutes',
        'ai_code',
        'ai_image',
        'ai_chat',
        'ai_sppech_to_text',
        'available_template',
        
    ];

    public static $arrDuration = [
        'unlimited' => 'Lifetime',
        'month' => 'Per Month',
        'year' => 'Per Year',
    ];

    public function status()
    {
        return [
            __('Lifetime'),
            __('Per Month'),
            __('Per Year'),
        ];
    }

    public static function total_plan()
    {
        return Plan::count();
    }

    public static function most_purchese_plan()
    {
        $free_plan = Plan::where('price', '<=', 0)->first()->id;

        return User:: select(DB::raw('count(*) as total'))->where('type', '=', 'company')->where('plan', '!=', $free_plan)->groupBy('plan')->first();
    }
}
