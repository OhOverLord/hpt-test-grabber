<?php

namespace HPT;

class HPTGrabber implements Grabber
{
    private array $productCache = [];

    private function getProductSearchUrl(string $productCode): string
    {
        return "https://www.czc.cz/" . $productCode . "/hledat";
    }

    private function getProductDetailUrl(string $productUrl): string
    {
        return "https://www.czc.cz/" . $productUrl;
    }

    private function getDOMPage(string $url): ?\DOMXPath
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        curl_close($ch);

        if ($curl_errno !== 0) {
            return null;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);

        return new \DOMXPath($dom);
    }
    private function getProduct(string $productId, string $productURL) : ?\DOMXPath {
        $productDetailURL = $this->getProductDetailUrl($productURL);
        $xpath = $this->getDOMPage($productDetailURL);
        if($xpath === null)
            return null;
        $codeNodeList = $xpath->query('//span[@class="pd-next-in-category__item-value"]');
        if ($codeNodeList->length > 0) {
            $code = $codeNodeList->item(0)->textContent;
            if($code !== $productId)
                return null;
        }
        return $xpath;
    }

    private function findProduct(string $productId): ?\DOMXPath
    {
        if (array_key_exists($productId, $this->productCache)) {
            return $this->productCache[$productId];
        }

        $url = $this->getProductSearchUrl($productId);
        $xpath = $this->getDOMPage($url);

        if ($xpath === null) {
            return null;
        }

        $productsNodeList = $xpath->query('//a[@class="tile-link"]');
        if ($productsNodeList) {
            foreach ($productsNodeList as $productNode) {
                $url = (string)$productNode->getAttribute("href");
                $product = $this->getProduct($productId, $url);
                $this->productCache[$productId] = $product;
                return $product;
            }
        }

        return null;
    }

    public function getPrice(string $productId): ?float
    {
        $xpath = $this->findProduct($productId);
        if($xpath === null)
            return null;
        $priceNodeList = $xpath->query('//span[@class="price-vatin"]');
        if ($priceNodeList->length > 0) {
            $price = $priceNodeList->item(0)->textContent;
            return floatval(str_replace(array('&nbsp;', 'Kč', ' ', '.', ','), '', htmlentities($price)));
        }
        return null;
    }

    public function getName(string $productId): ?string {
        $xpath = $this->findProduct($productId);
        if($xpath === null)
            return null;
        $nameNodeList = $xpath->query('//div[@class="pd-wrap"]/h1');
        if ($nameNodeList->length > 0) {
            return trim($nameNodeList->item(0)->textContent);
        }
        return null;
    }
    public function getRating(string $productId) : ?float {
        $xpath = $this->findProduct($productId);
        if($xpath === null)
            return null;
        $ratingNodeList = $xpath->query('//*[@id="product-detail"]/div[2]/div[1]/span');
        if ($ratingNodeList->length > 0) {
            $rating = $ratingNodeList->item(0)->textContent;
            return floatval(str_replace(array('&nbsp;', '%', ' '), '', htmlentities($rating)));
        }
        return null;
    }
}