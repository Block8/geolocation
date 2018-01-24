<?php

namespace Block8\Geolocation\AddressLocator;

use Block8\Geolocation\Coordinates;
use Block8\Geolocation\Exception\BadRequestException;
use Block8\Geolocation\Exception\HttpException;
use GuzzleHttp\Client;
use Block8\Geolocation\Address;

class PostcodeLocator
{
    const SERVICE_URI = 'https://api.postcodes.io/postcodes/';

    /** @var string */
    protected $postcode;

    public function __construct(string $postcode)
    {
        $postcode = strtoupper($postcode);
        $postcode = preg_replace('/([^A-Z0-9])/', '', $postcode);

        $this->postcode = $postcode;
    }

    public function locate() : Address
    {
        $response = null;

        try {
            $client = new Client();
            $response = $client->get(self::SERVICE_URI . $this->postcode);
        } catch (\Exception $ex) {
            throw new HttpException($ex->getMessage(), $ex->getCode(), $ex);
        }

        if (empty($response) || $response->getStatusCode() != 200) {
            throw new HttpException('Bad response from address location service.');
        }

        $data = json_decode($response->getBody(), true);

        if (!is_array($data) || !isset($data['status'])) {
            throw new HttpException('Bad response from address location service.');
        }

        if ($data['status'] == 500) {
            throw new HttpException('Bad response from address location service.');
        }

        if ($data['status'] == 400 || $data['status'] == 404) {
            throw new BadRequestException();
        }

        $coordinates = new Coordinates();
        $coordinates->eastings = $data['result']['eastings'];
        $coordinates->northings = $data['result']['northings'];
        $coordinates->latitude = $data['result']['latitude'];
        $coordinates->longitude = $data['result']['longitude'];

        $rtn = new Address();
        $rtn->town = $data['result']['admin_ward'];
        $rtn->county = $data['result']['admin_district'];
        $rtn->postcode = $data['result']['postcode'];
        $rtn->coordinates = $coordinates;

        return $rtn;
    }
}
