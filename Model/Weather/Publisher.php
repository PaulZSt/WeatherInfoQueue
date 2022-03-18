<?php

declare(strict_types=1);

namespace Elogic\WeatherInfoQueue\Model\Weather;

use Magento\Framework\MessageQueue\PublisherInterface;
use Elogic\WeatherInfo\Config\Config;
use Elogic\WeatherInfo\Model\Logger;

/**
 * Class Publisher
 *
 * @package Elogic\WeatherInfoQueue\Model\Weather
 */
class Publisher
{
    const TOPIC_NAME = 'weather.import';

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var Logger 
     */
    private $logger;

    /**
     * @var Config 
     */
    private $config;
    
    
    /**
     * Publisher constructor
     * 
     * @param PublisherInterface $publisher
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        PublisherInterface $publisher,
        Config $config,
        Logger $logger
    ) {
        $this->publisher = $publisher;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param $id
     */
    public function update(int $id)
    {
        try {
            $this->logger->debugData(sprintf('Send data to publish (ID = %s)', $id));
            $this->publisher->publish(self::TOPIC_NAME, $id);
        } catch (\Exception $e) {
            $this->logger->debugData(sprintf('Problem with add to queue ( ID =  %s ). Error %s',$id ,$e->getMessage()));
        }

    }
}
