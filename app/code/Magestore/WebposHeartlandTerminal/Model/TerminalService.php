<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposHeartlandTerminal\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magestore\WebposHeartlandTerminal\Api\ConnectedReaderRepositoryInterface;
use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface;
use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterfaceFactory;
use Magestore\WebposHeartlandTerminal\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class TerminalService
 *
 * @package Magestore\WebposHeartlandTerminal\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TerminalService implements \Magestore\WebposHeartlandTerminal\Api\TerminalServiceInterface
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var ConnectedReaderRepositoryInterface
     */
    protected $connectedReaderRepository;
    /**
     * @var ConnectedReaderInterfaceFactory
     */
    protected $connectedReaderFactory;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * TerminalService constructor.
     *
     * @param Data                                          $helper
     * @param ConnectedReaderRepositoryInterface            $connectedReaderRepository
     * @param ConnectedReaderInterfaceFactory               $connectedReaderFactory
     * @param LoggerInterface                               $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Data $helper,
        ConnectedReaderRepositoryInterface $connectedReaderRepository,
        ConnectedReaderInterfaceFactory $connectedReaderFactory,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->connectedReaderRepository = $connectedReaderRepository;
        $this->connectedReaderFactory = $connectedReaderFactory;
        $this->logger = $logger;
    }
    /**
     * Create new connected reader
     *
     * @return ConnectedReaderInterface
     */
    public function createNewConnectedReader()
    {
        return $this->connectedReaderFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function saveConnectedReader($request)
    {
        try {
            try {
                /** @var \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface $connectedReader */
                $connectedReader = $this->connectedReaderRepository->getConnectedReaderByPosId($request->getPosId());
            } catch (\Exception $e) {
                $connectedReader = $this->createNewConnectedReader();
            }

            if (!$connectedReader->getId()) {
                $connectedReader = $this->createNewConnectedReader();
            }

            /** @var ConnectedReaderInterface $request */
            $connectedReader->setPosId($request->getPosId());
            $connectedReader->setIpAddress($request->getIpAddress());
            $connectedReader->setPort($request->getPort());
            $connectedReader->setSerialPort($request->getSerialPort());
            $connectedReader->setConnectionMode($request->getConnectionMode());
            $this->connectedReaderRepository->save($connectedReader);
            return $connectedReader;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
    }
}
