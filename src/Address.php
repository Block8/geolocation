<?php

namespace Block8\Geolocation;

class Address
{
    /** @var string|null */
    public $postcode = null;

    /** @var Coordinates|null */
    public $coordinates = null;

    /** @var string|null */
    public $town;

    /** @var string|null */
    public $county;
}
