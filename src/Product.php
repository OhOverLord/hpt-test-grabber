<?php

declare(strict_types=1);

namespace HPT;
class Product {
    private string $code;
    private ?float $price;
    private ?string $name;
    private ?float $rating;

    public function __construct(string $code, ?float $price = null, ?string $name = null, ?float $rating = null) {
        $this->price = $price;
        $this->code = $code;
        $this->name = $name;
        $this->rating = $rating;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setRating(?float $rating): void
    {
        $this->rating = $rating;
    }
}