<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $requestData = [];

    protected function requestData(array $passedData = [])
    {
        return array_merge($this->requestData, $passedData);
    }
}
