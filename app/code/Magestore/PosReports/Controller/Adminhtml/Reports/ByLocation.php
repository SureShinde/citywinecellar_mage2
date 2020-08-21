<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Controller\Adminhtml\Reports;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class ByLocation
 *
 * Used to create By Location
 */
class ByLocation extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var string
     */
    protected $resource = 'Magestore_PosReports::location_breakdown';
}
