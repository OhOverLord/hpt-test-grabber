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
            $jsonData[$product->getCode()] = $product->getPrice() !== null ? array(
                'price' => $product->getPrice(),
                'name' => $product->getName(),
                'rating' => $product->getRating()) : null;
        }
        return json_encode($jsonData, JSON_PRETTY_PRINT);
    }
    public function addProduct(string $code, ?float $price = null, ?string $name = null, ?float $rating = null) : void {
        $this->productsArray[] = new Product($code, $price, $name, $rating);
    }
}