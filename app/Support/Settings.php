<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Key/value site settings: database overrides on top of config('site.defaults').
 * The merged set is cached until the next save.
 */
class Settings
{
    private const CACHE_KEY = 'site.settings';

    /** Keys whose values are stored encrypted at rest. */
    public const ENCRYPTED = ['mail_password'];

    /** Apply the SMTP server saved in Site Settings, if there is one. */
    public static function applyMailConfig(): void
    {
        $host = static::get('mail_host');

        if (blank($host)) {
            return; // fall back to MAIL_* in .env
        }

        $encryption = static::get('mail_encryption');

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) static::get('mail_port', 587),
            'mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
            'mail.mailers.smtp.username' => static::get('mail_username') ?: null,
            'mail.mailers.smtp.password' => static::get('mail_password') ?: null,
        ]);

        if (filled($from = static::get('mail_from_address'))) {
            config(['mail.from.address' => $from, 'mail.from.name' => static::get('site_name')]);
        }
    }

    /** Container key for the merged settings of the current request. */
    private const RESOLVED = 'site.settings.resolved';

    public static function all(): array
    {
        // Only the values saved in the admin are cached. Defaults are merged in on
        // every request, so a new default added in an update takes effect at once.
        // The merged result lives on the app container: one per request (and per test).
        $app = app();

        if (! $app->bound(self::RESOLVED)) {
            $app->instance(self::RESOLVED, array_merge(config('site.defaults', []), SiteTexts::defaults(), static::stored()));
        }

        return $app->make(self::RESOLVED);
    }

    /** @return array<string, mixed> values saved through the admin panel */
    private static function stored(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn () => Setting::query()->pluck('value', 'key')->all());
        } catch (Throwable) {
            // Table not migrated yet (fresh install): fall back to defaults.
            return [];
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::all()[$key] ?? null;

        if ($value !== null && $value !== '' && in_array($key, self::ENCRYPTED, true)) {
            try {
                return decrypt($value);
            } catch (Throwable) {
                return $default;
            }
        }

        return $value ?? $default;
    }

    public static function set(array $values): void
    {
        foreach ($values as $key => $value) {
            if (in_array($key, self::ENCRYPTED, true) && filled($value)) {
                $value = encrypt($value);
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        static::flush();
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        app()->forgetInstance(self::RESOLVED);
    }
}
