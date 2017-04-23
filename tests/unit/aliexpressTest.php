<?php
use app\components\AliExpressParser;

class aliexpressTest extends \Codeception\Test\Unit
{
    public $url = "https://ru.aliexpress.com/store/product/2015-new-super-wide-tire-bike-Snowmobile-ATV-26-bicycle-disc-brakes-bicycle-shock-absorbers-Russia/1803142_32344137835.html";

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $parser;
    protected $product = [];

    protected function _before()
    {
        require_once("/var/www/shop/components/AliExpressParser.php");
        $this->parser = new AliExpressParser();
        $this->product = $this->parser->getProduct($this->url);
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {

    }

    public function testName()
    {
        $this->assertNotNull($this->product['name']);
    }

    public function testUrl()
    {
        $this->assertNotNull($this->product['url']);
    }

    public function testCurrency()
    {
        $this->assertNotNull($this->product['currency']);
    }

    public function testPrice()
    {
        $this->assertNotNull($this->product['price']);
    }

    public function testPictures()
    {
        $this->assertNotNull($this->product['pictures']);
        $this->assertNotEmpty($this->product['pictures']);
    }

    public function testProperties()
    {
        $this->assertNotNull($this->product['properties']);
        $this->assertNotEmpty($this->product['properties']);
    }
}