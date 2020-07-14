<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposStripeTerminal\Api\Data;

/**
 * Interface ConnectedReaderInterface
 * @package Magestore\WebposStripeTerminal\Api\Data
 */
interface ConnectedReaderInterface
{
    const TABLE_ENTITY = 'webpos_stripe_terminal_connected_reader';
    const ID = 'id';
    const POS_ID = 'pos_id';
    const READER_ID = 'reader_id';
    const READER_LABEL = 'reader_label';
    const IP_ADDRESS = 'ip_address';
    const SERIAL_NUMBER = 'serial_number';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setId($value);

    /**
     * @return int|null
     */
    public function getPosId();

    /**
     * @param int|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setPosId($value);

    /**
     * @return string|null
     */
    public function getReaderId();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setReaderId($value);

    /**
     * @return string|null
     */
    public function getReaderLabel();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setReaderLabel($value);

    /**
     * @return string|null
     */
    public function getIpAddress();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setIpAddress($value);


    /**
     * @return string|null
     */
    public function getSerialNumber();

    /**
     * @param string|null $value
     * @return \Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setSerialNumber($value);
}