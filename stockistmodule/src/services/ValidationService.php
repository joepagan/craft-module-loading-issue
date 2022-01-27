<?php

namespace modules\stockistmodule\services;

use Craft;
use craft\base\Component;
use Exception;

class ValidationService extends Component
{
    public function model($model)
    {
        Craft::debug('', __METHOD__);
        if(!$model->validate()) {
            $errors = $model->getErrorSummary(true);
            $errorMessage = '';
            foreach ($errors as $error) {
                $errorMessage = $errorMessage . $error . '<br>';
            }
            Craft::error($errorMessage);
            throw new Exception($errorMessage);
        }
    }
}
