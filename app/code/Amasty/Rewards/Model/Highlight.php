<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Amasty\Rewards\Api\Data\HighlightInterface;

class Highlight extends \Magento\Framework\Model\AbstractModel implements HighlightInterface
{
    /**
     * {@inheritdoc}
     */
    public function isVisible()
    {
        return $this->_getData(self::VISIBLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setVisible($isVisible)
    {
        $this->setData(self::VISIBLE, $isVisible);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptionColor()
    {
        return $this->_getData(self::CAPTION_COLOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setCaptionColor($captionColor)
    {
        $this->setData(self::CAPTION_COLOR, $captionColor);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptionText()
    {
        return $this->_getData(self::CAPTION_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCaptionText($captionText)
    {
        $this->setData(self::CAPTION_TEXT, $captionText);

        return $this;
    }
}
