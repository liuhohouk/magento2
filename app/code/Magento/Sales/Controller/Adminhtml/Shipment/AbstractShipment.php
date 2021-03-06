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
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Controller\Adminhtml\Shipment;

class AbstractShipment extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Sales\Controller\Adminhtml\Shipment
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_shipment')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Shipments'), __('Shipments'));
        return $this;
    }

    /**
     * Shipments grid
     */
    public function indexAction()
    {
        $this->_title->add(__('Shipments'));

        $this->_initAction()
            ->_addContent($this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Shipment'));
        $this->_view->renderLayout();
    }

    /**
     * Shipment information page
     */
    public function viewAction()
    {
        if ($shipmentId = $this->getRequest()->getParam('shipment_id')) {
            $this->_forward('view', 'order_shipment', null, array('come_from'=>'shipment'));
        } else {
            $this->_forward('noroute');
        }
    }

    public function pdfshipmentsAction()
    {
        $shipmentIds = $this->getRequest()->getPost('shipment_ids');
        if (!empty($shipmentIds)) {
            $shipments = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Shipment\Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $shipmentIds))
                ->load();
            if (!isset($pdf)) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
            } else {
                $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
                $pdf->pages = array_merge($pdf->pages, $pages->pages);
            }
            $date = $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s');
            return $this->_fileFactory->create(
                'packingslip' . $date . '.pdf',
                $pdf->render(),
                \Magento\Filesystem::VAR_DIR,
                'application/pdf'
            );
        }
        $this->_redirect('sales/*/');
    }

    public function printAction()
    {
        /** @see \Magento\Sales\Controller\Adminhtml\Order\Invoice */
        $shipmentId = $this->getRequest()->getParam('invoice_id');
        if ($shipmentId) { // invoice_id o_0
            $shipment = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($shipmentId);
            if ($shipment) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')
                    ->getPdf(array($shipment));
                $date = $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s');
                return $this->_fileFactory->create(
                    'packingslip' . $date . '.pdf',
                    $pdf->render(),
                    \Magento\Filesystem::VAR_DIR,
                    'application/pdf'
                );
            }
        } else {
            $this->_forward('noroute');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }
}
