<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;
use Magento\Directory\Model\Config\Source\Country as SourceCountry;

/**
 * Class ClosedSession
 * @package Magestore\Webpos\Ui\DataProvider\Pos\Form\Modifier
 */
class ClosedSession extends AbstractModifier
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magestore\Webpos\Model\Pos\Pos
     */
    protected $currentPos;

    protected $groupLabel = 'Closed Session';
    protected $sortOrder = 20;
    protected $groupContainer = 'closed_session';

    /**
     * ClosedSession constructor.
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->coreRegistry = $registry;
    }

    /**
     * Get current pos
     *
     * @return \Magestore\Webpos\Model\Pos\Pos`
     */
    public function getCurrentPos()
    {
        if (!$this->currentPos)
            $this->currentPos = $this->coreRegistry->registry('current_pos');
        return $this->currentPos;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        $pos = $this->getCurrentPos();
        if ($pos) {
            $data = $pos->getData();
            $this->loadedData[$pos->getId()] = $data;
        }
        return $this->loadedData;
    }

    /**
     * get visible
     *
     * @param
     * @return
     */
    public function getVisible()
    {
        $pos = $this->getCurrentPos();
        if (!$pos || !$pos->getId())
            return false;
        return true;
    }

    /**
     * modify data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                $this->groupContainer => [
                    'children' => $this->getChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __($this->groupLabel),
                                'autoRender' => true,
                                'collapsible' => true,
                                'visible' => $this->getVisible(),
                                'opened' => false,
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->sortOrder
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    public function getChildren()
    {
        $children = [
            'closed_session' => $this->getClosedSession(),
        ];
        return $children;
    }

    public function getClosedSession()
    {
        $listingTarget = 'webpos_closed_session_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $listingTarget,
                        'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                        'ns' => $listingTarget,
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'posId' => '${ $.provider }:data.pos_id',
                        ],
                        'exports' => [
                            'posId' => '${ $.externalProvider }:params.pos_id',
                        ]
                    ],
                ],
            ],
        ];
    }
}