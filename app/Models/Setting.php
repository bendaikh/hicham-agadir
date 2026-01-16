<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'label',
        'description'
    ];

    protected $casts = [
        'value' => 'array', // Automatically convert JSON to array
    ];

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = [])
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value, $label = null, $description = null)
    {
        // Ensure value is an array if it's not already
        if (!is_array($value)) {
            $value = [$value];
        }
        
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value, // Laravel will automatically JSON encode due to the 'array' cast
                'label' => $label,
                'description' => $description
            ]
        );
    }

    /**
     * Get article categories
     */
    public static function getArticleCategories()
    {
        return self::getValue('article_categories', ['PMMA', 'Vitrage', 'Profilé', 'Accessoire']);
    }

    /**
     * Get article types
     */
    public static function getArticleTypes()
    {
        return self::getValue('article_types', ['DIFFUSANT', 'TRANSPARENT', 'OPALINE']);
    }
}
