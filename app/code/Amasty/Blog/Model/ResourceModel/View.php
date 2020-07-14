<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Amasty\Blog\Api\Data\ViewInterface;

/**
 * Class View
 */
class View extends AbstractDb
{
    const TABLE_NAME = 'amasty_blog_views';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, ViewInterface::VIEW_ID);
    }

    /**
     * @param ViewInterface $object
     * @param int $postId
     * @param string $sessionId
     *
     * @return $this
     */
    public function loadByPostAndSession($object, $postId, $sessionId)
    {
        $select = $this->getConnection()->select()
            ->from(['views' => $this->getTable(self::TABLE_NAME)])
            ->where('views.' . ViewInterface::POST_ID . ' = :post_id')
            ->where('views.' . ViewInterface::SESSION_ID . ' = :session_id');
        $view = $this->getConnection()->fetchRow($select, [':post_id' => $postId, ':session_id' => $sessionId]);
        if (is_array($view)) {
            $object->addData($view);
        }

        return $this;
    }
}
