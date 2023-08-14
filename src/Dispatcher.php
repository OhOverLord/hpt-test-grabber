<?php

declare(strict_types=1);

namespace HPT;

use Exception;

class Dispatcher
{
    private Grabber $grabber;
    private Output $output;
    private string $inputFilePath = "input.txt";

    public function __construct(Grabber $grabber, Output $output)
    {
        $this->grabber = $grabber;
        $this->output = $output;
    }

    /**
     * @throws Exception
     */
    public function readInputFile() : array {
        if (!file_exists($this->inputFilePath)) {
            throw new Exception("File does not exist.");
        }
        $lines = file($this->inputFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new Exception("Error reading file.");
        }
        return $lines;
    }

    /**
     * @return string JSON
     */
    public function run(): string
    {
        try {
            $codes = $this->readInputFile();
            foreach ($codes as $code) {
                $price = $this->grabber->getPrice($code);
                $name = $this->grabber->getName($code);
                $rating = $this->grabber->getRating($code);
                $this->output->addProduct($code, $price, $name, $rating);
            }
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
        return $this->output->getJson();
    }
}
