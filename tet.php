<?php

require_once('vendor/autoload.php');

$ipAddresses = [
    '81.158.185.179',
    '178.79.130.57',
    '::ffff:81.153.194.127',
    '2a00:23c0:9286:c400:856d:8b7:db5a:c7f2',
    '2400:cb00:2048:1::681c:f27',
];

foreach ($ipAddresses as $address) {
    $location = \Block8\Geolocation\AddressLocator::locateFromString($address);
    print $address . ' is located in ' . $location->town . PHP_EOL;
}
