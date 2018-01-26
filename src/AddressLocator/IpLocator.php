<?php

namespace Block8\Geolocation\AddressLocator;

use Block8\Geolocation\Coordinates;
use Block8\Geolocation\Exception\BadRequestException;
use Block8\Geolocation\Exception\HttpException;
use GuzzleHttp\Client;
use Block8\Geolocation\Address;

class IpLocator
{
    const SERVICE_URI = 'https://api.ipdata.co/';

    /** @var string */
    protected $ip;

    public function __construct(string $ip)
    {
        if (!self::isValidIp($ip)) {
            throw new BadRequestException($ip . ' is not a valid IP address.');
        }

        $this->ip = $this->cleanIp($ip);
    }

    public static function isValidIp(string $ip)
    {
        return !(filter_var($ip, FILTER_VALIDATE_IP) === false);
    }

    protected function cleanIp(string $ip)
    {
        if (preg_match('/\:\:ffff\:([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/', $ip)) {
            $ip = substr($ip, 7);
        }

        return $ip;
    }

    public function locate() : Address
    {
        $response = null;

        try {
            $client = new Client();
            $response = $client->get(self::SERVICE_URI . $this->ip);
        } catch (\Exception $ex) {
            throw new HttpException($ex->getMessage(), $ex->getCode(), $ex);
        }

        if (empty($response) || $response->getStatusCode() != 200) {
            throw new HttpException('Bad response from address location service.');
        }

        $data = json_decode($response->getBody(), true);

        if (!is_array($data)) {
            throw new HttpException('Bad response from address location service.');
        }

        $coordinates = new Coordinates();
        $coordinates->latitude = $data['latitude'];
        $coordinates->longitude = $data['longitude'];

        $rtn = new Address();
        $rtn->town = $data['city'];
        $rtn->county = $data['region'];
        $rtn->postcode = $data['postal'];
        $rtn->coordinates = $coordinates;

        return $rtn;
    }
}
