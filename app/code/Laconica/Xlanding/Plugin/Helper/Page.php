<?php

namespace Laconica\Xlanding\Plugin\Helper;

class Page
{
    public function afterPrepareResultPage(
        \Amasty\Xlanding\Helper\Page $subject,
        $result
    ) {
        if ($result) {
            $result->getConfig()->addBodyClass('catalog-category-view');
        }
        return $result;
    }
}