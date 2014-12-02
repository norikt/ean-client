<?php
namespace Otg\Ean\Result;

use GuzzleHttp\HasDataTrait;

class Result {

    use HasDataTrait;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function hasKey($name)
    {
        return isset($this->data[$name]);
    }
}