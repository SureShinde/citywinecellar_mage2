<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Magestore\Rewardpoints\Model\Shipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Save checkout fields with quote shipping address.
 *
 * @package  Temando\Shipping\Observer
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
class SaveCheckoutFieldsObserver implements ObserverInterface
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $modelManager;

    /**
     * SaveCheckoutFieldsObserver constructor.
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressFactory
     */
    public function __construct(
        \Magento\Framework\Module\Manager $modelManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->modelManager = $modelManager;
        if($this->modelManager->isEnabled('Temando_Shipping')) {
            $this->addressRepository = $objectManager->get('Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface');
            $this->addressFactory = $objectManager->get('Temando\Shipping\Api\Data\Checkout\AddressInterfaceFactory');
        }
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Api\Data\AddressInterface|\Magento\Quote\Model\Quote\Address $quoteAddress */
        $quoteAddress = $observer->getData('quote_address');
        if ($quoteAddress->getAddressType() !== \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING) {
            return;
        }

        if (!$quoteAddress->getExtensionAttributes()) {
            return;
        }

        // persist checkout fields
        try {
            $checkoutAddress = $this->addressRepository->getByQuoteAddressId($quoteAddress->getId());
        } catch (NoSuchEntityException $e) {
            $checkoutAddress = $this->addressFactory->create(['data' => [
                \Temando\Shipping\Api\Data\Checkout\AddressInterface::SHIPPING_ADDRESS_ID => $quoteAddress->getId(),
            ]]);
        }

        $extensionAttributes = $quoteAddress->getExtensionAttributes();
        if( $extensionAttributes instanceof \Magento\Quote\Api\Data\AddressExtension ){
            $checkoutAddress->setServiceSelection($extensionAttributes->getCheckoutFields());
            $this->addressRepository->save($checkoutAddress);
        }
    }
}
