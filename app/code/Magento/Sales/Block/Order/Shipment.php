<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order view block
 */
namespace Magento\Sales\Block\Order;

class Shipment extends \Magento\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/shipment.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Payment\Helper\Data $paymentHelper,
        array $data = array()
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_coreRegistry = $registry;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Order # %1', $this->getOrder()->getRealOrderId()));
        }
        $this->setChild(
            'payment_info',
            $this->_paymentHelper->getInfoBlock($this->getOrder()->getPayment())
        );
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->getUrl('*/*/history');
        }
        return $this->getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */
    public function getBackTitle()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return __('Back to My Orders');
        }
        return __('View Another Order');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getInvoiceUrl($order)
    {
        return $this->getUrl('*/*/invoice', array('order_id' => $order->getId()));
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    /**
     * @param object $order
     * @return string
     */
    public function getCreditmemoUrl($order)
    {
        return $this->getUrl('*/*/creditmemo', array('order_id' => $order->getId()));
    }

    /**
     * @param object $shipment
     * @return string
     */
    public function getPrintShipmentUrl($shipment)
    {
        return $this->getUrl('*/*/printShipment', array('shipment_id' => $shipment->getId()));
    }

    /**
     * @param object $order
     * @return string
     */
    public function getPrintAllShipmentsUrl($order)
    {
        return $this->getUrl('*/*/printShipment', array('order_id' => $order->getId()));
    }
}
