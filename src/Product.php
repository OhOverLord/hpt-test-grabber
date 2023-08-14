<?php

declare(strict_types=1);

namespace HPT;
class Product {
    private ?float $price;
    private string $code;

    public function __construct(?float $price = null, string $code) {
        $this->price = $price;
        $this->code = $code;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}