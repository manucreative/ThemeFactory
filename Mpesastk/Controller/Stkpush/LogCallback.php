<?php
namespace ThemeFactory\Mpesastk\Controller\Stkpush;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;

class LogCallback extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    protected $request;


    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Http $request
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/LogValidation.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);


        $data = file_get_contents('php://input');

        $recievedData = json_decode($data, true);

        $logger->info('Validation: ' . print_r($recievedData, true));

    }

}