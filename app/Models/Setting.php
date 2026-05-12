<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(function (self $setting) {
            Cache::forget("setting.{$setting->key}");
        });

        static::deleted(function (self $setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }

    public static function get($key, $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            try {
                $setting = self::where('key', $key)->value('value');
                return $setting ?? $default;
            } catch (\Throwable) {
                return $default;
            }
        });
    }

    public static function set($key, $value): self
    {
        $setting = self::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
        return $setting;
    }
}
