<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Service;

/**
 * Class TagService
 * @package Magestore\OrderSuccess\Service
 */
class TagService
{
    
    /**
     * @var \Magestore\OrderSuccess\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magestore\OrderSuccess\Api\Data\TagSourceInterface
     */
    protected $tagSourceInterface;

    /**
     * TagService constructor.
     * @param \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository
     * @param \Magestore\OrderSuccess\Api\Data\TagSourceInterface $tagSourceInterface
     */
    public function __construct(
        \Magestore\OrderSuccess\Api\OrderRepositoryInterface $orderRepository,
        \Magestore\OrderSuccess\Api\Data\TagSourceInterface $tagSourceInterface
        )
    {
        $this->orderRepository = $orderRepository;
        $this->tagSourceInterface = $tagSourceInterface;
    }

    /**
     * Add Sales Tag
     *
     * @param string $tag
     * @param int $orderId
     * @return string
     */
    public function addMassOrderTag($tag, $orderId)
    {
        $order = $this->orderRepository->getById($orderId);
        if($order->getId()) {
            if(strpos($order->getTagColor(), $tag) === false) {
                $tag = '#' . $tag;
                if ($order->getTagColor())
                    $tag = $order->getTagColor() . ',' . $tag;
                $order->setTagColor($tag);
            }
            $this->orderRepository->save($order);
        }
        return $order->getTagColor();
    }

    /**
     * Add Tag for Sales Collection
     *
     * @param string $tag
     * @param array $orderIds
     * @return $this
     */
    public function addTagByOrderIds($tag, $orderIds)
    {
        foreach($orderIds as $orderId) {
            $this->addMassOrderTag($tag, $orderId);
        }
        return $this;
    }

    /**
     * Add Tag for order
     *
     * @param string $tag
     * @param int $orderId
     * @return string
     */
    public function addTagForAnOrder($tag, $orderId)
    {
        $order = $this->orderRepository->getById($orderId);
        if($order->getId()) {
            $tags = explode(',', $order->getTagColor());
            if(!in_array($tag, $tags)) {
                if ($order->getTagColor())
                    $tag = $order->getTagColor() . ',' . $tag;
                $order->setTagColor($tag);
            }
            $this->orderRepository->save($order);
        }
        return $order->getTagColor();
    }

    /**
     * Add Orders Tag
     *
     * @param string $tag
     * @param array $orderIds
     * @return $this
     */
    public function addOrdersTag($tag, $orderIds)
    {
        $this->orderRepository->massUpdateTag($orderIds, $tag);
        return $this;
    }

    /**
     * Remove Order Tag
     *
     * @param int $orderId
     * @return string
     */
    public function removeOrderTag($orderId)
    {
        $order = $this->orderRepository->getById($orderId);
        if($order->getId()) {
            $order->setTagColor('');
            $this->orderRepository->save($order);
        }
        return $order->getTagColor();
    }

    /**
     * Remove Orders Tag
     *
     * @param array $orderIds
     * @return $this
     */
    public function removeOrdersTag($orderIds)
    {
        $this->orderRepository->massUpdateTag($orderIds, '');
        return $this;
    }

    /**
     * @return array
     */
    public function getTagArray()
    {
        return $this->tagSourceInterface->getOptionArray();
    }

    /**
     * Get tag label
     *
     * @return string
     */
    public function getTagLabel($tag)
    {
        $tagList = $this->getTagArray();
        $label = isset($tagList[$tag]) ? $tagList[$tag] : '#FFFFFF';
        return $label;
    }

}