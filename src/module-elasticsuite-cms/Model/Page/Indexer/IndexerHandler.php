<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile_ElasticSuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ElasticSuiteCms\Model\Page\Indexer;

use Smile\ElasticSuiteCatalog\Model\Eav\Indexer\IndexerHandler as AbstractIndexer;

/**
 * Indexing operation handling for ElasticSearch engine.
 *
 * @category  Smile
 * @package   Smile_ElasticSuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 */
class IndexerHandler extends AbstractIndexer
{
    const INDEX_NAME = 'cms_page';
    const TYPE_NAME  = 'page';

    /**
     * {@inheritDoc}
     */
    public function __construct(
        \Smile\ElasticSuiteCore\Api\Index\IndexOperationInterface $indexOperation,
        \Magento\Framework\Indexer\SaveHandler\Batch $batch,
        $indexName = self::INDEX_NAME,
        $typeName = self::TYPE_NAME
    ) {
        parent::__construct($indexOperation, $batch, $indexName, $typeName);
    }
}
