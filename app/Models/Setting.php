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
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        // If value is JSON array and default is array, return array
        if (is_array($setting->value) && is_array($default)) {
            return $setting->value;
        }
        
        // Otherwise return the value as stored
        return $setting->value;
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value, $label = null, $description = null)
    {
        // For business_name and business_logo, wrap in array for JSON storage
        // (since model uses 'array' cast, we need to store as array)
        if (in_array($key, ['business_name', 'business_logo'])) {
            $arrayValue = is_array($value) ? $value : [$value];
            return self::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $arrayValue, // Store as array (will be JSON encoded)
                    'label' => $label,
                    'description' => $description
                ]
            );
        }
        
        // For other settings, ensure value is an array
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

    /**
     * Get business name
     */
    public static function getBusinessName()
    {
        $value = self::getValue('business_name', ['']);
        // Unwrap from array (stored as array due to cast)
        return is_array($value) ? ($value[0] ?? '') : $value;
    }

    /**
     * Get business logo path
     */
    public static function getBusinessLogo()
    {
        $value = self::getValue('business_logo', ['']);
        // Unwrap from array (stored as array due to cast)
        return is_array($value) ? ($value[0] ?? '') : $value;
    }
}
