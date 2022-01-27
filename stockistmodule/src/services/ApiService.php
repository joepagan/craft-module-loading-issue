<?php
namespace modules\stockistmodule\services;

use Craft;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use modules\stockistmodule\responses\GoogleGeocodingResponse;
use yii\base\Component;

use craft\feedme\events\FeedProcessEvent;
use yii\base\Exception;

class ApiService extends Component
{
    private \GuzzleHttp\Client $client;
    private string $apiKey;
    private string $apiUrl = "https://maps.googleapis.com/maps/api/geocode/json";
    private string $geocodeType;

    public function __construct()
    {
        $this->client = Craft::createGuzzleClient();
        $this->apiKey = Craft::parseEnv('$GOOGLE_MAPS_GEOCODING_API_KEY');
        $this->geocodeType = 'address';
    }

    /**
     * @throws Exception
     */
    public function getCoordinates(string $urlEncodedAddress)
    {
        $response = $this->request($urlEncodedAddress);
        $googleGeocodingResponse = new GoogleGeocodingResponse();
        $googleGeocodingResponse->map($response);
        return $googleGeocodingResponse;
    }

    private function request(string $payload)
    {
        Craft::debug('', __METHOD__);
        $fullUrl = "{$this->apiUrl}?key={$this->apiKey}&{$this->geocodeType}=${payload}";
        Craft::debug("Google API request: {$fullUrl}", __METHOD__);
        try {
            $response = $this->client->request('GET', $fullUrl);
        }
        catch (RequestException | ConnectException | ClientException | ServerException $e) {
            throw new Exception($e->getMessage());
        }
        $responseBody = json_decode($response->getBody(), true);
        Craft::debug('Google API response: ' . json_encode($responseBody), __METHOD__);
        return $responseBody;
    }
}
