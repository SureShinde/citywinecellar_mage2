<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Controller\Adminhtml\Reports;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class ByStaff
 *
 * Used to create By Staff
 */
class ByStaff extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var string
     */
    protected $resource = 'Magestore_PosReports::sales_by_staff';
}
