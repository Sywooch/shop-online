<?php

namespace app\components;

class AliExpressParser
{

    /** @var string */
    private $_data;

    public function getProduct($url)
    {
        $this->loadData($url);

        $product = [];
        $product['url'] = preg_replace("#^\/\/#", "http://", $this->parseUrl($this->_data));
        $product['seo_url'] = $this->parseSeoUrl($product['url']);
        $product['name'] = $this->parseName($this->_data);
        $product['image'] = $this->parseImage($this->_data);
        $product['price'] = str_replace(",", ".", preg_replace("#(\&nbsp\;|[\s]+)#", "", $this->parsePrice($this->_data)));
        $product['currency'] = $this->parseCurrency($this->_data);

        return $product;
    }

    public function getPictures($url)
    {
        $this->loadData($url);
        return $this->parsePictures($this->_data);
    }

    public function getProperties($url)
    {
        $this->loadData($url);
        return $this->parseProperties($this->_data);
    }

    protected function download($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt"); // cookies storage / here the changes have been made
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // false for https
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $result = curl_exec($ch);

//        echo curl_error($ch);
//        print_r(curl_getinfo($ch));

        curl_close($ch);
        return $result;
    }

    protected function loadData($url, $reload = false)
    {
        if ($reload || !$this->_data) {
            $this->_data = $this->download($url);
            while ($this->_data && !$this->parseName($this->_data)) {
                $this->_data = $this->download($this->parseLocation($this->_data));
            }
        }
        return $this->_data;
    }

    protected function parseLocation($data)
    {
        if (preg_match('#Location:[\s]*([^\s\r\n]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    protected function parseName($data)
    {
        if (preg_match('#\<h1[^\>]+itemprop\=\"name\"\>([^\<]+)#i', $data, $match)) {
           return $match[1];
        }
        return null;
    }

    protected function parseCurrency($data)
    {
        if (preg_match('#itemprop\=\"priceCurrency\" content\=\"([^\"]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    protected function parsePrice($data)
    {
        if (preg_match('#itemprop\=\"price\"\>([^\<]+)#i', $data, $match)) {
            return $match[1];
        } elseif (preg_match('#itemprop\=\"lowPrice\"\>([^\<]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    protected function parseImage($data)
    {
        if (preg_match('#window\.runParams\.mainBigPic \= \"([^\"]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    protected function parsePictures($data)
    {
        if (preg_match('#window\.runParams\.imageBigViewURL\=\[([^\]]+)#i', $data, $match)) {
            if (preg_match_all('#\"([^\s\"\,]+)#i', $match[1], $pictures)) {
                return $pictures[1];
            }
        }
        return null;
    }

    protected function parseProperties($data)
    {
        if (preg_match('#<ul[^\>]+product-property-list[^\>]+>([\s\S]+)</ul>#iU', $data, $match)) {
            // <li[^\>]+>[\s]*  [\s]*</li>
            if (preg_match_all('#<span class=\"propery-title\">([^\<]+):</span>[\s]*'
                . '<span class=\"propery-des\"[^\>]+>([^\<]+)</span>#iU', $match[1], $items)
            ) {
                return [$items[1], $items[2]];
            }
        }
        return null;
    }

    protected function parseUrl($data)
    {
        if (preg_match('#\<meta property\=\"og\:url\" content=\"([^\?\"]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    protected function parseSeoUrl($data)
    {
        if (preg_match('#\/([^\/]+)\/[^\/]+$#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

}
