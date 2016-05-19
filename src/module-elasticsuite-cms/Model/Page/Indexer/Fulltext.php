<?php
/**
 * DISCLAIMER
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

use Magento\Framework\Search\Request\DimensionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticSuiteCms\Model\Page\Indexer\Fulltext\Action\Full;

/**
 * Categories fulltext indexer
 *
 * @category Smile
 * @package  Smile_ElasticSuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Fulltext implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var string
     */
    const INDEXER_ID = 'elasticsuite_cms_fulltext';

    /** @var array index structure */
    protected $data;

    /**
     * @var IndexerHandler
     */
    private $indexerHandler;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    /**
     * @var Full
     */
    private $fullAction;

    /**
     * @param Full                  $fullAction       The full index action
     * @param IndexerHandler        $indexerHandler   The index handler
     * @param StoreManagerInterface $storeManager     The Store Manager
     * @param DimensionFactory      $dimensionFactory The dimension factory
     * @param array                 $data             The data
     */
    public function __construct(
        Full $fullAction,
        IndexerHandler $indexerHandler,
        StoreManagerInterface $storeManager,
        DimensionFactory $dimensionFactory,
        array $data
    ) {
        $this->fullAction = $fullAction;
        $this->indexerHandler = $indexerHandler;
        $this->storeManager = $storeManager;
        $this->dimensionFactory = $dimensionFactory;
        $this->data = $data;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids The ids
     *
     * @return void
     */
    public function execute($ids)
    {
$logger = \Magento\Framework\App\ObjectManager::getInstance()->create('\Psr\Log\LoggerInterface');
$logger->debug('execute ');
        $storeIds = array_keys($this->storeManager->getStores());
        /** @var IndexerHandler $saveHandler */
        $saveHandler = $this->indexerHandler;
        foreach ($storeIds as $storeId) {
            $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $saveHandler->deleteIndex([$dimension], new \ArrayObject($ids));
            $saveHandler->saveIndex([$dimension], $this->fullAction->rebuildStoreIndex($storeId, $ids));
        }
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $storeIds = array_keys($this->storeManager->getStores());

        /** @var IndexerHandler $saveHandler */
        $saveHandler = $this->indexerHandler;

        foreach ($storeIds as $storeId) {
            $dimension = $this->dimensionFactory->create(['name' => 'scope', 'value' => $storeId]);
            $saveHandler->cleanIndex([$dimension]);
            $saveHandler->saveIndex([$dimension], $this->fullAction->rebuildStoreIndex($storeId));
        }

    }

    /**
     * {@inheritDoc}
     */
    public function executeList(array $categoryIds)
    {
        $this->execute($categoryIds);
    }

    /**
     * {@inheritDoc}
     */
    public function executeRow($categoryId)
    {
        $this->execute([$categoryId]);
    }
}
