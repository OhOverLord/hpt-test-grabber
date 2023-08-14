<?php

namespace HPT;

class HPTGrabber implements Grabber
{
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

    private function findProduct(string $productId) : ?\DOMXPath {
        $url = $this->getProductSearchUrl($productId);
        $xpath = $this->getDOMPage($url);
        if($xpath === null)
            return null;
        $productsNodeList = $xpath->query('//a[@class="tile-link"]');
        if ($productsNodeList) {
            foreach ($productsNodeList as $productNode) {
                $url = (string)$productNode->getAttribute("href");
                return $this->getProduct($productId, $url);
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
}