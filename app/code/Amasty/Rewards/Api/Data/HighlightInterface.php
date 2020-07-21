<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface HighlightInterface
{
    const VISIBLE = 'visible';
    const CAPTION_COLOR = 'caption_color';
    const CAPTION_TEXT = 'caption_text';

    /**
     * @return bool
     */
    public function isVisible();

    /**
     * @param bool $visible
     *
     * @return HighlightInterface
     */
    public function setVisible($visible);

    /**
     * @return string|null
     */
    public function getCaptionColor();

    /**
     * @param string $captionColor
     *
     * @return HighlightInterface
     */
    public function setCaptionColor($captionColor);

    /**
     * @return string|null
     */
    public function getCaptionText();

    /**
     * @param string $captionText
     *
     * @return HighlightInterface
     */
    public function setCaptionText($captionText);
}
