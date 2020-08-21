<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Controller\Adminhtml\Reports;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class ByAllLocation
 *
 * Used to create By All Location
 */
class ByAllLocation extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var string
     */
    protected $resource = 'Magestore_PosReports::location_overview';
}
