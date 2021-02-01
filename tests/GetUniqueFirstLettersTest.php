<?php

use PHPUnit\Framework\TestCase;

class GetUniqueFirstLettersTest extends TestCase
{
    /**
     * @dataProvider positiveDataProvider
     */
    public function testPositive($input, $expected)
    {
        $this->assertEquals($expected, getUniqueFirstLetters($input));
    }

    public function positiveDataProvider()
    {
        return [
            [
                [
                    [
                        "name" => "Albuquerque Sunport International Airport",
                        "code" => "ABQ",
                        "city" => "Albuquerque",
                        "state" => "New Mexico",
                        "address" => "2200 Sunport Blvd, Albuquerque, NM 87106, USA",
                        "timezone" => "America/Los_Angeles",
                    ],
                    [
                        "name" => "Atlanta Hartsfield International Airport",
                        "code" => "ATL",
                        "city" => "Atlanta",
                        "state" => "Georgia",
                        "address" => "6000 N Terminal Pkwy, Atlanta, GA 30320, USA",
                        "timezone" => "America/New_York",
                    ],
                    [
                        "name" => "Austin Bergstrom International Airport",
                        "code" => "AUS",
                        "city" => "Austin",
                        "state" => "Texas",
                        "address" => "3600 Presidential Blvd, Austin, TX 78719, USA",
                        "timezone" => "America/Los_Angeles",
                    ],
                    [
                        "name" => "Nashville Metropolitan Airport 1",
                        "code" => "BNA",
                        "city" => "Nashville",
                        "state" => "Tennessee",
                        "address" => "1 Terminal Dr, Nashville, TN 37214, USA",
                        "timezone" => "America/Chicago",
                    ],
                    [
                        "name" => "Boise Airport",
                        "code" => "BOI",
                        "city" => "Boise",
                        "state" => "Idaho",
                        "address" => "3201 W Airport Way #1000, Boise, ID 83705, USA",
                        "timezone" => "America/Denver",
                    ]
                ],
                ['A','B','N']
            ],
            [
                [
                    [
                        "name" => "Tucson Airport",
                        "code" => "TUS",
                        "city" => "Tucson",
                        "state" => "Arizona",
                        "address" => "7250 S Tucson Blvd, Tucson, AZ 85756, USA",
                        "timezone" => "America/Phoenix",
                    ],
                    [
                        "name" => "Texarkana Regional Airport",
                        "code" => "TXK",
                        "city" => "Texarkana",
                        "state" => "Arkansas",
                        "address" => "201 Airport Dr, Texarkana, AR 71854, USA",
                        "timezone" => "America/Chicago",
                    ],
                    [
                        "name" => "Knoxville Airport",
                        "code" => "TYS",
                        "city" => "Knoxville",
                        "state" => "Tennessee",
                        "address" => "2055 Alcoa Hwy, Alcoa, TN 37701, USA",
                        "timezone" => "America/New_York",
                    ],
                    [
                        "name" => "Northeast Florida Regional Airport",
                        "code" => "UST",
                        "city" => "St. Augustine",
                        "state" => "Florida",
                        "address" => "4900 US-1, St. Augustine, FL 32095, USA",
                        "timezone" => "America/New_York",
                    ],
                    [
                        "name" => "Southern California Logistics Airport",
                        "code" => "VCV",
                        "city" => "Victorville",
                        "state" => "California",
                        "address" => "18374 Phantom W, Victorville, CA 92394, USA",
                        "timezone" => "America/Los_Angeles",
                    ],
                    [
                        "name" => "Valdez Airport",
                        "code" => "VDZ",
                        "city" => "Valdez",
                        "state" => "Alaska",
                        "address" => "300 Airport Rd, Valdez, AK 99686, USA",
                        "timezone" => "America/Anchorage",
                    ]
                ],
                ['K','N','S','T','V']
            ],
        ];
    }
}