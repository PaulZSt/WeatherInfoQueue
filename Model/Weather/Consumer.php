<?php

declare(strict_types=1);

namespace Elogic\WeatherInfoQueue\Model\Weather;

use Elogic\WeatherInfo\Model\WeatherFactory;
use Elogic\WeatherInfo\Service\Weather\WeatherApi;
use Elogic\WeatherInfo\Api\WeatherRepositoryInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Elogic\WeatherInfo\Config\Config;
use Elogic\WeatherInfo\Model\Logger;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Consumer
 *
 * @package Elogic\WeatherInfoQueue\Model\Weather
 */
class Consumer
{

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
     * @var WeatherApi
     */
    private $weatherApi;

    /**
     * @var WeatherFactory
     */
    private $weatherFactory;

    /**
     * @var WeatherRepositoryInterface
     */
    private $weatherRepository;

    /**
     * @var Json
     */
    private $json;

    /**
     * Consumer constructor
     *
     * @param PublisherInterface $publisher
     * @param Config $config
     * @param Logger $logger
     * @param WeatherApi $weatherApi
     * @param WeatherFactory $weatherFactory
     * @param WeatherRepositoryInterface $weatherRepository
     * @param Json $json
     */
    public function __construct(
        PublisherInterface $publisher,
        Config $config,
        Logger $logger,
        WeatherApi $weatherApi,
        WeatherFactory $weatherFactory,
        WeatherRepositoryInterface $weatherRepository,
        Json $json
    ) {
        $this->publisher = $publisher;
        $this->config = $config;
        $this->logger = $logger;
        $this->weatherApi = $weatherApi;
        $this->weatherFactory = $weatherFactory;
        $this->weatherRepository = $weatherRepository;
        $this->json = $json;
    }

    /**
     * @param $id
     * @return bool
     */
    public function update(int $id): bool
    {

        if (!$this->config->isEnabled()) {
            return false;
        }

        try {
            $message = 'Consumer execution';

            $data = $this->weatherApi->getWeather();
            $weather = $this->weatherFactory->create();
            $weather->setData([
                'weather' => $this->json->serialize($data)
            ]);
            $this->weatherRepository->save($weather);

        } catch (\Exception $e) {
            $message = sprintf('Weather consumer exception: %s', $e->getMessage());
        }
        
        $this->logger->debugData($message);
        return true;
    }
}
