<?php

namespace App\Services;

use App\Models\SiteSetting;

class SiteSettingService {

    private $settings;

    public function __construct() {
        $this->settings = SiteSetting::all()->keyBy("key");
    }

    public function get($key, $default = null) {
        return $this->settings->get($key)?->value ?? $default;
    }

    public function set($key, $value) {
        
        // Update or Create the setting in the database
        $setting = SiteSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Update the in-memory settings array
        $this->settings[$key] = SiteSetting::where('key', $key)->first();
    }

    public function all() {
        return $this->settings->map(fn ($setting) => $setting->value);
    }

}