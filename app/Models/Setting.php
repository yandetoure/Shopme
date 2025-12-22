<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'site_email',
        'logo',
        'slogan',
        'address',
        'phone',
        'email_contact',
        'navbar_color',
        'navbar_text_color',
        'primary_color',
        'secondary_color',
        'text_color',
        'background_color',
        'font_family',
        'heading_font',
        'currency',
        'tax_rate',
        'default_shipping',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'default_shipping' => 'decimal:2',
    ];

    /**
     * Récupérer les paramètres (singleton)
     */
    public static function getSettings()
    {
        return static::firstOrCreate(['id' => 1]);
    }

    /**
     * Mettre à jour les paramètres
     */
    public static function updateSettings(array $data)
    {
        $settings = static::getSettings();
        $settings->update($data);
        return $settings;
    }
}
