<?php

namespace App\Services;

class GeoService
{
    public function isValid(?string $latitude, ?string $longitude): bool
    {
        if ($latitude === null || $longitude === null || $latitude === '' || $longitude === '') {
            return false;
        }
        return is_numeric($latitude) && is_numeric($longitude)
            && (float) $latitude >= -90 && (float) $latitude <= 90
            && (float) $longitude >= -180 && (float) $longitude <= 180;
    }

    public function googleMapsUrl(?string $latitude, ?string $longitude): ?string
    {
        if (!$this->isValid($latitude, $longitude)) {
            return null;
        }
        return 'https://www.google.com/maps?q=' . $latitude . ',' . $longitude;
    }
}
