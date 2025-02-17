<?php

namespace ThemeFactory\Mpesastk\Model;


class MpesastkConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{

    protected $myMethodCode = 'themeFactory_mpesastk';

    protected $escaper;

    protected $customerSession;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->escaper = $escaper;
        $this->cart = $cart;
        $this->method = $paymentHelper->getMethodInstance($this->myMethodCode);
        $this->customerSession = $customerSession;
    }

        public function getConfig()
            {
                return $this->method->isAvailable() ? [
                    'payment' => [
                        $this->myMethodCode => [
                            'customerName' => $this->getCustomerName(),
                            'customerPhone' => $this->getCustomerPhone(),
                            'paybillCurrency' => $this->getCurrency(),
                            'confirmPayment' => 'stkcallback/stkpush/paymentConfirmation',
                            'startPayValidation' => 'stkcallback/stkpush/index',
                            'recheckPayment' => 'stkcallback/stkpush/recheckpayment',
                            'paymentComplete' => 'stkcallback/stkpush/complete',
                            'payReference' => $this->getPayQouteId(),
                            'paybillNo' => $this->getPayPaybill(),
                            'mpesaLimit' => 70000,
                        ],
                    ],
                ] : [];
            }


    protected function getCustomerName()
    {
        return $this->customerSession->getCustomer()->getName();
    }

    protected function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    protected function getCustomerPhone()
    {
        //return $this->customerSession->getCustomer()->getPrimaryBillingAddress()->getTelephone();
    }

    protected function getCurrency()
    {
        /*
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $currencysymbol = $objectManager->get('Magento\Directory\Model\Currency');
        return $currencysymbol->getCurrencySymbol();
        */
        return 'KES';
    }

    protected function getPayQouteId()
    {
        return $this->cart->getQuote()->getId();
    }

    protected function getPayPaybill()
    {
        return $this->method->getPayPaybill();
    }

}