<?php

namespace Laconica\Catalog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\Product\Visibility;

class UpdateProductVisibility extends Command
{

    protected $connection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        $name = null
    )
    {
        $this->connection = $resourceConnection->getConnection();
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('la:catalog:update-product-visibility')
            ->setDescription('Update product visibility according to pos visibility');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started:');

        // Get webpos_visible attribute id
        $posAttributeIdSelect = $this->connection->select()
            ->from('eav_attribute', ['attribute_id'])
            ->where('attribute_code = ?', 'webpos_visible');
        $posAttributeId = $this->connection->fetchOne($posAttributeIdSelect);

        $output->writeln("webpost_visible attribute ID: {$posAttributeId}");

        // Get visibility attribute id
        $visibilityIdSelect = $this->connection->select()
            ->from('eav_attribute', ['attribute_id'])
            ->where('attribute_code = ?', 'visibility');
        $visibilityId = $this->connection->fetchOne($visibilityIdSelect);

        $output->writeln("visibility attribute ID: {$visibilityId}");

        if (!$posAttributeId || !$visibilityId) {
            $output->writeln("Missing required attributes");
            return;
        }

        // Get webpos_visible products
        $productsPosVisibleSelect = $this->connection->select()
            ->distinct(true)
            ->from('catalog_product_entity_int', ['entity_id'])
            ->where('attribute_id = ?', (int)$posAttributeId)
            ->where('value = ?', 1);
        $productsPosVisible = $this->connection->fetchCol($productsPosVisibleSelect);

        $output->writeln("Products count visible in POS: " . count($productsPosVisible));

        // Count products to update
        $selectUpdateCount = $this->connection->select()
            ->distinct(true)
            ->from('catalog_product_entity_int', ['entity_id'])
            ->where('attribute_id = ?', $visibilityId)
            ->where('entity_id IN(?)', $productsPosVisible)
            ->where('value != ?', Visibility::VISIBILITY_NOT_VISIBLE);
        $updateCount = $this->connection->fetchCol($selectUpdateCount);

        $output->writeln("Products will be setted invisible on websites: " . count($updateCount));

        // Set products that visible in webpos not visible in website
        if (!empty($updateCount)) {
            $this->connection->update('catalog_product_entity_int', [
                'value' => Visibility::VISIBILITY_NOT_VISIBLE
            ], [
                'attribute_id = ?' => $visibilityId,
                'entity_id IN (?)' => $productsPosVisible,
                'value != ?' => Visibility::VISIBILITY_NOT_VISIBLE
            ]);
            $output->writeln("Products updated: " . count($updateCount));
        }

        $output->writeln('Finished.');
    }
}