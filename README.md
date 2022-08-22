<p align="center">
  <a href="https://www.abyssale.com?utm_source=github&utm_medium=logo&utm_campaign=php" target="_blank">
    <img src="https://uploads-ssl.webflow.com/6214efb2d4b5d94158f2ff03/6218f45ff39a58c8dbf7eb2c_abyssale-logo.svg" alt="Sentry" width="199" height="44">
  </a>
</p>

# Abyssale SDK for PHP

Generate images at scale, based on templates created in [Abyssale]([https://abyssale.com).
This PHP SDK handles the communication with Abyssale API.

To have a better understanding of how Abyssale works from a developer point of view, you can check our [Abyssale developers portal](https://developers.abyssale.com/).

## Installation

### Tl;DR

```
composer require php-http/curl-client guzzlehttp/psr7 abyssale/abyssale
```

### Step by step

This library does not have a dependency on a specific library that sends HTTP requests. We use HTTPlug to achieve the decoupling. You have to choose what library to use for sending HTTP requests (see the list of packages that support `php-http/client-implementation`).
You can use the basic curl implementation : 

```
composer require php-http/curl-client
```

You do also need to install a PSR-7 implementation and a factory to create PSR-7 messages.
You can use Guzzle PSR-7 implementation :

```
composer require guzzlehttp/psr7
```

And finally, you can install this library by running the following:

```
composer require abyssale/abyssale
```

## Usage

### Get your API key

In order to use the Abyssale API, you need to get your API key from [Abyssale > Settings > API](https://app.abyssale.com/settings/api-key).

### Instantiate the Client

```php
$client = new Abyssale\Client('api_key');
```

If you don't want to rely on discovery for your PSR-7 implementation, it's possible to specify a `Psr\Http\Client\ClientInterface` as 2nd argument, a `Psr\Http\Message\RequestFactoryInterface` as 3rd argument and a `Psr\Http\Message\StreamFactoryInterface` as 4th argument.

### Generate images 

```php
$templateId = "42ddb879-b894-41bc-896e-3edc8b3e33d2"; // Id of the template from which you want to generate an image

$elementsForParis = [
    'city_name' => [ // name of the layer (in this case, this is a text layer)
        'payload' => 'Paris'
    ],
    'main_picture' => [ // name of the layer (in this case, this is an image layer)
        'image_url' => 'https://acme.com/img/paris-eiffel-tower.jpeg'
    ],
];
$imageParis = $client->generateImage($templateId, 'instagram-story', $elementsForParis);
echo $imageParis->getUrl();

$elementsForSeattle = [
    'city_name' => [
        'payload' => 'Seattle'
    ],
    'main_picture' => [
        'image_url' => 'https://acme.com/img/seattle-space-needle.jpeg'
    ],      
];
$imageSeattle = $client->generateImage($templateId, 'instagram-story', $elementsForSeattle);
echo $imageSeattle->getUrl();
```

#### Options

You can pass to the `generateImage` method a 4th argument that contains different options :

```php
$image = $client->generateImage(
    $templateId, 
    'instagram-story', 
    $elementsForSeattle,
    [
        'image_type' => 'png',
        'compression_level' => 75,
    ]    
);
```

| Option name | Description
| ----------- | -----------
| image_type | string. `jpeg` or `png`. Default to Abyssale logic.
| compression_level | int between 1 and 100. Level of compression applied to the result image. Default is 95.

### Generate a PDF

You can only generate a PDF from a template format of type `print`.

```php
$templateId = "a51a9c5e-ab14-47d8-8d3e-521074273d8b"; // Id of the template from which you want to generate a PDF

$elementsToOverride = [
    'employee_lastname' => [
        'payload' => 'John Doe'
    ],
];
$pdf = $client->generatePdf($templateId, 'a4', $elementsToOverride);
echo $pdf->getUrl();
```

### Generate asynchronously multiples template formats

If you want to generate multiple template formats in only one API call, you must use the [asynchronous generation method](https://developers.abyssale.com/rest-api/image-generation/generate-multi-format-images). 

#### Images

```php
$templateId = "42ddb879-b894-41bc-896e-3edc8b3e33d2"; // Id of the template from which you want to generate an image

$elementsToOverride = [
    'city_name' => [ // name of the layer (in this case, this is a text layer)
        'payload' => 'Paris'
    ],
    'main_picture' => [ // name of the layer (in this case, this is an image layer)
        'image_url' => 'https://acme.com/img/paris-eiffel-tower.jpeg'
    ],
];
$generationRequest = $client->asyncGenerateImage(
    $templateId,
    ['instagram-story', 'facebook-post'],
    $elementsToOverride,
    'https://webhook.acme.com/endpoint-images', // your specific webhook url
    [] // image options, see above
);
echo $generationRequest->getId();
```

#### PDF

```php
$templateId = "a51a9c5e-ab14-47d8-8d3e-521074273d8b"; // Id of the template from which you want to generate an image

$elementsToOverride = [
    'employee_lastname' => [
        'payload' => 'John Doe'
    ],
];
$generationRequest = $client->asyncGeneratePdf(
    $templateId,
    ['a4', 'letter'],
    $elementsToOverride,
    'https://webhook.acme.com/endpoint-images' // your specific webhook url
);
echo $generationRequest->getId();
```
