<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Amp;

class ContentModificator
{
    /**
     * @var array
     */
    private $prohibitedTags = [
        '<noscript',
        '<base',
        '<img',
        '<picture',
        '<video',
        '<audio',
        '<iframe',
        '<frame',
        '<frameset',
        '<object',
        '<param',
        '<applet',
        '<embed',
        '<frame',
    ];

    /**
     * @var array
     */
    private $prohibitedAttributes = [
        '(href="javascript:[^`"]*")' => 'href="#"',
        '(_self)' => '_blank',
        '(_parent)' => '_blank',
        '(_top)' => '_blank',
    ];

    /**
     * @param $html
     *
     * @return string
     */
    public function validateHtml($html)
    {
        $html = $this->removeScripts($html);
        $html = $this->removeForbiddenContent($html);
        $html = $this->removeForbiddenAttributes($html);

        return $html;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function removeScripts($content)
    {
        $tag = 'script';
        $content = preg_replace_callback(
            '#(\<' . $tag . '[^\>]*\>)(.*?)(\<\/' . $tag . '\>)|(\<' . $tag . '[^\>]*)(.*?)\>#ims',
            function ($matches) {
                $content = $matches[0] ?? '';
                if (strpos($content, 'type="application/json"') === false
                    && strpos($content, 'custom-element="amp') === false
                    && strpos($content, 'ampproject') === false
                ) {

                    $content = '';
                }

                return $content;
            },
            $content
        );

        return $content;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function removeForbiddenContent($content)
    {
        foreach ($this->prohibitedTags as $tag) {
            if (strpos($content, $tag) !== false) {
                $tag = trim($tag, '<');
                $content = preg_replace(
                    '#(\<' . $tag . '[^\>]*\>)(.*?)(\<\/' . $tag . '\>)|(\<' . $tag . '[^\>]*)(.*?)\>#ims',
                    '',
                    $content
                );
            }
        }

        return $content;
    }

    /**
     * @param $content
     * @return string
     */
    private function removeForbiddenAttributes($content)
    {
        foreach ($this->prohibitedAttributes as $attribute => $replace) {
            $content = preg_replace($attribute, $replace, $content);
        }

        return $content;
    }
}
