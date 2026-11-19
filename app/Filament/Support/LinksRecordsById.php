<?php

namespace App\Filament\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * For admin sections whose records have a different public web address.
 *
 * Bookings are reached on the website by reference (/booking/OT-...),
 * destinations, packages, pages and tour types by slug. The admin panel looks them up by id
 * ($recordRouteKeyName = 'id', so links keep working after a slug is edited).
 * Without this, any admin link built from a record (dashboard widget, search
 * results, the redirect after saving) would carry the public key and lead to
 * a "404 Not Found" page.
 */
trait LinksRecordsById
{
    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        if (($parameters['record'] ?? null) instanceof Model) {
            $record = $parameters['record'];
            $parameters['record'] = $record->getAttribute(static::getRecordRouteKeyName() ?? $record->getKeyName());
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters, $configuration);
    }
}
