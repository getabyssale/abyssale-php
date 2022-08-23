<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$client = new \Abyssale\Client($_SERVER["ABYSSALE_API_KEY"]);

$airlines = [
    'paris-amsterdam-89' => ['Paris - Amsterdam', 'Starting from 89€'],
    'sandiego-seattle-130' => ['San Diego - Seattle', 'Rain for 130$ only'],
    'vancouver-calgary-65' => ['Vancouver - Calgary', 'As cheap as 65$'],
];

foreach ($airlines as $key => $line) {
    $image = $client->generateImage('cb15eff9-15cb-4cf3-b48e-654dc7619f35', 'facebook-post', [
        'from_to_text' => [
            'payload' => $line[0],
        ],
        'price_text' => [
            'payload' => $line[1],
        ]
    ]);
    echo $key . " : " . $image->getUrl() . "\r\n";
    sleep(1); // to avoid rate-limiting
}
