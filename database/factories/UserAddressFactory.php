<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserAddress::class, function (Faker $faker) {

	$addresses = [
		["NSW", "Sydney", "Blacktown"],
		["NSW", "Sydney", "Penrith"],
		["NSW", "Sydney", "Castle hill"],
		["QLD", "Brsibane", "Toowong"],
		["QLD", "Brsibane", "St Lucia"],
		["VIC", "Melbourne", "Dandenong"],
		["VIC", "Melbourne", "Frankton"],
	];

	$address = $faker->randomElement($addresses);


    return [
        'province' => $address[0],
        'city' => $address[1],
        'district' => $address[2],
        'address'  => $faker->streetAddress,
        'zip' => $faker->postcode,
        'contact_name' => $faker->name,
        'contact_phone' => $faker->phoneNumber,
    ];
});
