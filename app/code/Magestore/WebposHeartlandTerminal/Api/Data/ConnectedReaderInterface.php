<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposHeartlandTerminal\Api\Data;

/**
 * Interface ConnectedReaderInterface
 * @package Magestore\WebposHeartlandTerminal\Api\Data
 */
interface ConnectedReaderInterface
{
    /**
     * Define constrains
     */
    const TABLE_ENTITY = 'webpos_heartland_terminal_connected_reader';
    const ID = 'id';
    const POS_ID = 'pos_id';
    const IP_ADDRESS = 'ip_address';
    const PORT = 'port';
    const SERIAL_PORT = 'serial_port';
    const CONNECTION_MODE = 'connection_mode';

    /**
     * GetId
     *
     * @return int|null
     */
    public function getId();

    /**
     * SetId
     *
     * @param int|null $value
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setId($value);

    /**
     * GetPosId
     *
     * @return int|null
     */
    public function getPosId();

    /**
     * SetPosId
     *
     * @param int|null $value
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setPosId($value);

    /**
     * Get IpAddress
     *
     * @return string|null
     */
    public function getIpAddress();

    /**
     * Set IpAddress
     *
     * @param string|null $value
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setIpAddress($value);

    /**
     * Get Port
     *
     * @return string|null
     */
    public function getPort();

    /**
     * Set Port
     *
     * @param string|null $value
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setPort($value);
    /**
     * Get SerialPort
     *
     * @return string|null
     */
    public function getSerialPort();
    /**
     * Set SerialPort
     *
     * @param string|null $value
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setSerialPort($value);
    /**
     * Get ConnectionMode
     *
     * @return string|null
     */
    public function getConnectionMode();
    /**
     * Set ConnectionMode
     *
     * @param string|null $value
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface
     */
    public function setConnectionMode($value);
}
