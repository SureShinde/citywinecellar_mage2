<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Controller\Adminhtml\Reports;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class ByPaymentMethod
 *
 * Used to create By Payment Method
 */
class ByPaymentMethod extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var string
     */
    protected $resource = 'Magestore_PosReports::sales_by_payment_method';
}
