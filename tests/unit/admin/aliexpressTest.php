<?php
use app\components\AliExpressParser;

class aliexpressTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $parser;
    protected $product = [];
    protected $pictures = [];
    protected $properties = [];

    protected function _before()
    {
        $data = file("data/goods.txt");
        $url = array_shift($data);

        $this->parser = new AliExpressParser();
        $this->product = $this->parser->getProduct($url);
        $this->pictures = $this->parser->getPictures($url);
        $this->properties = $this->parser->getProperties($url);
    }

    protected function _after()
    {
    }

    // tests
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
        $this->assertNotNull($this->pictures);
        $this->assertNotEmpty($this->pictures);
    }

    public function testProperties()
    {
        $this->assertNotNull($this->properties);
        $this->assertNotEmpty($this->properties);
    }
}