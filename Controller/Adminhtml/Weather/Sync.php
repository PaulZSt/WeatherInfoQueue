<?php
declare(strict_types=1);

namespace Elogic\WeatherInfoQueue\Controller\Adminhtml\Weather;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;
use Elogic\WeatherInfoQueue\Model\Weather\Publisher;
use Elogic\WeatherInfo\Model\Logger;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class Sync
 *
 * @package Elogic\WeatherInfoQueue\Controller\Adminhtml\Weather
 */
class Sync extends Action
{
    /**
     * Const Synthetic Id
     */
    const TEMP_ID = 123;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * @var Publisher 
     */
    private $publisher;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Publisher $publisher
     * @param UrlInterface $urlInterface
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Publisher $publisher,
        UrlInterface $urlInterface,
        Logger $logger
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->publisher = $publisher;
        $this->urlInterface = $urlInterface;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        try {
            $message = __('Wether import scheduled');
            $this->messageManager->addSuccessMessage($message);
            $this->publisher->update(self::TEMP_ID);
            $this->logger->debugData($message);

        } catch (\Exception $e) {
            $message = sprintf('Wether import error: %s', $e->getMessage());
            $this->messageManager->addErrorMessage($message);
            $this->logger->debugData($message);

        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());

    }

}
