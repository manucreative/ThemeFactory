<?php
namespace ThemeFactory\ExportInvoices\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as OptionCollectionFactory;

class VendorSales extends Column implements OptionSourceInterface
{
    protected $orderFactory;
    protected $productRepository;
    protected $eavConfig;
    protected $attributeRepository;
    protected $attributeFactory;
    protected $optionCollectionFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderFactory $orderFactory,
        ProductRepository $productRepository,
        AttributeRepository $attributeRepository,
        AttributeFactory $attributeFactory,
        OptionCollectionFactory $optionCollectionFactory,
        array $components = [],
        array $data = [],
        Config $eavConfig
    ) {
        $this->orderFactory = $orderFactory;
        $this->productRepository = $productRepository;
        $this->eavConfig = $eavConfig;
        $this->attributeRepository = $attributeRepository;
         $this->attributeFactory = $attributeFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
{

    if (isset($dataSource['data']['items'])) {
        
        foreach ($dataSource['data']['items'] as &$item) {
            // Modify your logic to ensure invoice_id is available
            if (isset($item['order_id'])) {
                // Load order by order ID
                $order = $this->orderFactory->create()->load($item['order_id']);
                if ($order->getId()) {
                    $productIds = [];
                    // Get all items in the order
                    foreach ($order->getAllItems() as $orderItem) {
                        $productIds[] = $orderItem->getProductId();
                    }

                    $sellerValues = [];
                    foreach ($productIds as $productId) {
                        try {
                            $product = $this->productRepository->getById($productId);
                            $sellerId = $product->getData('vendor_seller');
                                // Get label of vendor_seller attribute option
                                $sellerLabel = $this->getAttributeValue($sellerId);
                                $sellerValues[] = $sellerLabel ?: __('Seller Not Found');

                        } catch (NoSuchEntityException $e) {
                            // Log error or handle accordingly
                            $sellerValues[] = __('Seller Not Found');
                        }
                    }

                    // Check if all products have the same seller value
                    if (count(array_unique($sellerValues)) === 1) {
                        $item[$this->getData('name')] = reset($sellerValues);
                    } else {
                        $item[$this->getData('name')] = __('Mixed');
                    }
                } else {
                    $item[$this->getData('name')] = __('Order Not Found');
                }
            } else {
                $item[$this->getData('name')] = __('Order ID not found');
            }
        }
    }

    return $dataSource;
}


   public function toOptionArray()
{
    $options = [];
    $attributeCode = 'vendor_seller';

    // Load the vendor_seller attribute
    $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);

        $options = $attribute->getSource()->getAllOptions();

    return $options;
}
    
private function getAttributeValue($vendorSeller)
{
    $attribute = $this->eavConfig->getAttribute('catalog_product', 'vendor_seller');
    $options = $attribute->getSource()->getAllOptions();

    foreach ($options as $option) {
        if ($option['value'] == $vendorSeller) {
            return $option['label'];
        }
    }

    return null;
}
}

