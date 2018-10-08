<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserAddress::class, function (Faker $faker) {

	$addresses = [
		["NSW", "Blacktown"],
		["NSW", "Penrith"],
		["NSW", "Castle hill"],
		["QLD", "Toowong"],
		["QLD", "St Lucia"],
		["VIC", "Dandenong"],
		["VIC", "Frankton"],
	];

	$address = $faker->randomElement($addresses);


    return [
        'state' => $address[0],
        'suburb' => $address[1],
        'address'  => $faker->streetAddress,
        'postcode' => $faker->postcode,
        'contact_name' => $faker->name,
        'contact_phone' => $faker->phoneNumber,
    ];
});
