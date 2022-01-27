<?php
namespace modules\stockistmodule\services;

use Craft;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use modules\stockistmodule\StockistModule;
use yii\base\Component;

use craft\feedme\events\FeedProcessEvent;
use yii\base\Exception;

class ElementService extends Component
{
    private \GuzzleHttp\Client $client;

    public function process(FeedProcessEvent $event)
    {
        Craft::debug('', __METHOD__);
        $element = $event->element;
        $address = $event->contentData['addressInformation']['new1']['fields']['address1'];
        $urlEncodedAddress = urlencode($address);
        $geocode = StockistModule::getInstance()->apiService->getCoordinates($urlEncodedAddress);
        StockistModule::getInstance()->validationService->model($geocode);

        $element->setFieldValues([
            'addressInformation' => [
                'new1' => [
                    'type' => 'address',
                    'fields' => [
                        'address1' => "{$geocode->street_number} {$geocode->route} {$geocode->locality}",
                        'city' => $geocode->postal_town,
                        'county' => $geocode->administrative_area_level_1,
                        'postcode' => $geocode->postal_code,
                    ],
                ],
            ],
            'coordinates' => [
                'new1' => [
                    'type' => 'coordinates',
                    'fields' => [
                        'latitude' => $geocode->lat,
                        'longitude' => $geocode->lng,
                    ],
                ],
            ],
        ]);
    }
}
