<?php

namespace app\components;

class AliExpressParser
{

    /** @var string */
    private $_data;

    /**
     * Возвращает основные свойства товара
     *
     * @param $url
     * @return array
     */
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
        $product['rating'] = $this->parseRating($this->_data);

        return $product;
    }

    /**
     * Возвращает массив фотографий
     *
     * @param $url
     * @return array|null
     */
    public function getPictures($url)
    {
        $this->loadData($url);
        return $this->parsePictures($this->_data);
    }

    /**
     * Возвращает массив свойств товара вида [names[], values[]]
     * @param $url
     * @return array|null
     */
    public function getProperties($url)
    {
        $this->loadData($url);
        return $this->parseProperties($this->_data);
    }

    /**
     * Возвращает массив свойств для обновления (цена и валюта)
     * todo в будущем добавить: наличие, доставку, рейтинг, отзывы
     * @param $url
     * @return array
     */
    public function getProductUpdate($url)
    {
        $this->loadData($url);

        $product = [];
        $product['price'] = str_replace(",", ".", preg_replace("#(\&nbsp\;|[\s]+)#", "", $this->parsePrice($this->_data)));
        $product['currency'] = $this->parseCurrency($this->_data);
        $product['rating'] = $this->parseRating($this->_data);

        return $product;
    }

    /**
     * Возвращает массив отзывов покупателей
     * @param $url
     * @return array|null
     */
    public function getProductFeedback($url)
    {
        $this->loadData($url);

        $urlFeedback = $this->parseFeedbackUrl($this->_data);
        $dataFeedback = $this->download($urlFeedback);

        return $this->parseFeedback($dataFeedback);
    }


    protected function download($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // false for https
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }

    /**
     * Возвращает загруженные данные со страницы товара, не смотря на переадресации
     * fix для CURLOPT_FOLLOWLOCATION=0
     *
     * @param $url
     * @param bool $reload
     * @return mixed|string
     */
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

    /**
     * Возвращает URL при переадресации
     *
     * @param $data
     * @return null
     */
    protected function parseLocation($data)
    {
        if (preg_match('#Location:[\s]*([^\s\r\n]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Возвращает название товара
     *
     * @param $data
     * @return string|null
     */
    protected function parseName($data)
    {
        if (preg_match('#\<h1[^\>]+itemprop\=\"name\"\>([^\<]+)#i', $data, $match)) {
           return $match[1];
        }
        return null;
    }

    /**
     * Возвращает валюту цены товара
     *
     * @param $data
     * @return string|null
     */
    protected function parseCurrency($data)
    {
        if (preg_match('#itemprop\=\"priceCurrency\" content\=\"([^\"]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Возвращает цену товара
     *
     * @param $data
     * @return string|null
     */
    protected function parsePrice($data)
    {
        if (preg_match('#itemprop\=\"price\"\>([^\<]+)#i', $data, $match)) {
            return $match[1];
        } elseif (preg_match('#itemprop\=\"lowPrice\"\>([^\<]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Возвращает главное фото товара
     *
     * @param $data
     * @return string|null
     */
    protected function parseImage($data)
    {
        if (preg_match('#window\.runParams\.mainBigPic \= \"([^\"]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Возвращает массив URL фотографий товара
     *
     * @param $data
     * @return array|null
     */
    protected function parsePictures($data)
    {
        if (preg_match('#window\.runParams\.imageBigViewURL\=\[([^\]]+)#i', $data, $match)) {
            if (preg_match_all('#\"([^\s\"\,]+)#i', $match[1], $pictures)) {
                return $pictures[1];
            }
        }
        return null;
    }

    /**
     * Возвращает массив свойств товара вида [names[], values[]]
     *
     * @param $data
     * @return array|null
     */
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

    /**
     * Возвращает URL страници товара на AliExpress
     *
     * @param $data
     * @return string|null
     */
    protected function parseUrl($data)
    {
        if (preg_match('#\<meta property\=\"og\:url\" content=\"([^\?\"]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }


    /**
     * Возвращает seoUrl
     *
     * @param $data
     * @return string|null
     */
    protected function parseSeoUrl($data)
    {
        if (preg_match('#\/([^\/]+)\/[^\/]+$#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Возвращает рейтинг
     *
     * @param $data
     * @return null
     */
    protected function parseRating($data)
    {
        if (preg_match('#percent-num">([\d\.]+)#i', $data, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Возвращает URL для страницы feedback
     *
     * @param $data
     * @return null|string
     */
    protected function parseFeedbackUrl($data)
    {
        if (preg_match('#thesrc="([^\"]+)#i', $data, $match)) {
            return "https:" . $match[1];
        }
        return null;
    }

    /**
     * Возвращает массив данных с отзывами покупателей
     *
     * @param $data
     * @return array|null
     */
    protected function parseFeedback($data)
    {
        if (preg_match_all('#feedback-item([\s\S]+)(?=feedback-item|complex-pager)#iU', $data, $feedbackItems)) {
            $feedback = [];
            foreach ($feedbackItems[1] as $i => $feedbackItem) {
                // get name
                if (!preg_match('#user-name">([\s\S]+)</span>#iU', $feedbackItem, $name)) {
                    continue;
                }
                $feedback[$i]['buyer'] = trim(strip_tags($name[1]));

                // get text
                if (!preg_match('#<dt class="buyer-feedback">([\s\S]+)</dt>#iU', $feedbackItem, $text)) {
                    continue;
                }
                $feedback[$i]['text'] = trim(strip_tags($text[1]));

                // get date
                if (!preg_match('#<dd class="r-time">([^\<]+)</dd>#iU', $feedbackItem, $date)) {
                    continue;
                }
                $feedback[$i]['date'] = trim(strip_tags($date[1]));

                // get photo
                if (!preg_match_all('#data-src="([^\"]+)#i', $feedbackItem, $photos)) {
                    continue;
                }
                $feedback[$i]['photos'] = $photos[1];
            }
            return $feedback;
        }
        return null;
    }

}
