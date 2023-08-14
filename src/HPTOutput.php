<?php

namespace HPT;

class HPTOutput implements Output
{
    private array $productsArray;
    public function __construct() {
        $this->productsArray = array();
    }
    public function getJson(): string {
        $jsonData = [];
        foreach ($this->productsArray as $product) {
            $jsonData[$product->getCode()] = ["price" => $product->getPrice()];
        }
        return json_encode($jsonData, JSON_PRETTY_PRINT);
    }
    public function addProduct(?float $price, string $code) : void {
        $this->productsArray[] = new Product($price, $code);
    }
}