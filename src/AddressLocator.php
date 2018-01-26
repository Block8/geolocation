<?php

namespace Block8\Geolocation;

use Block8\Geolocation\AddressLocator\IpLocator;
use Block8\Geolocation\AddressLocator\PostcodeLocator;

class AddressLocator
{
    public static function locateFromString(string $address) : Address
    {
        if (IpLocator::isValidIp($address)) {
            return self::locateFromIP($address);
        }
        if (preg_match('/([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/', $address)) {
            return self::locateFromIP($address);
        }

        if (preg_match('/\:\:ffff\:([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/', $address)) {
            $address = substr($address, 7);
            return self::locateFromIP($address);
        }

        if (preg_match('/^[a-zA-Z]{1,2}([0-9]{1,2}|[0-9][a-zA-Z])\s*[0-9][a-zA-Z]{2}$/', $address)) {
            return self::locateFromPostcode($address);
        }

        return self::locateFromAddress($address);
    }

    public static function locateFromAddress(Address $address) : Address
    {

    }

    public static function locateFromPostcode(string $postcode) : Address
    {
        $locator = new PostcodeLocator($postcode);
        return $locator->locate();
    }

    public static function locateFromIP(string $ip) : Address
    {
        $locator = new IpLocator($ip);
        return $locator->locate();
    }
}
