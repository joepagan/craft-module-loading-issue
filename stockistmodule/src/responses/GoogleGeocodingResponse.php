<?php

namespace modules\stockistmodule\responses;

use Craft;
use craft\base\Model;

class GoogleGeocodingResponse extends Model
{
    public string $status;
    public string $error_message;
    public string $street_number;
    public string $route;
    public string $postal_code;
    public string $administrative_area_level_1;
    public string $locality;
    public string $administrative_area_level_2;
    public string $country;
    public string $formatted_address;
    public string $place_id;
    public float $lng;
    public float $lat;

    public function map($data)
    {
        $this->status = $data->status;
        if ($this->status !== 'OK') {
            $this->error_message = $data->error_message;
            Craft::error($this->error_message);
            throw new \Exception($this->error_message, 500);
        }
        $results = $data->results;
        foreach ($results as $result) {
            if ($result->types[0] !== 'street_address') {
                continue;
            }
            $addressComponents = $result->address_components;
            foreach ($addressComponents as $component) {
                switch  ($component->types[0]) {
                    case 'street_number':
                        $this->street_number = $component->long_name;
                    case 'route':
                        $this->route = $component->long_name;
                    case 'locality':
                        $this->locality = $component->long_name;
                    case 'administrative_area_level_2':
                        $this->administrative_area_level_2 = $component->long_name;
                    case 'administrative_area_level_1':
                        $this->administrative_area_level_1 = $component->long_name;
                    case 'country':
                        $this->country = $component->long_name;
                    case 'postal_code':
                        $this->postal_code = $component->long_name;
                }
            }
            $this->formatted_address = $result->formatted_address;
            $this->lat = $result->geometry->location->lat;
            $this->lng = $result->geometry->location->lng;
            $this->place_id = $result->place_id;
            break;
        }
    }

    /**
     * Returns the validation rules for attributes.
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        /**
         * Required
         */
        $rules[] = [[
        ], 'required'];

        /**
         * Strings
         */
        $rules[] = [[
        ], 'string'];

        return $rules;
    }
}
