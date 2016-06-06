<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\ElasticsuiteCms\Plugin\Indexer\Page\Save;

use Magento\Framework\Indexer\IndexerRegistry;
use Smile\ElasticsuiteCms\Model\Page\Indexer\Fulltext;
use Magento\Cms\Model\Page;

/**
 * Plugin that proceed cms page reindex in ES after cms page save
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 */
class ReindexPageAfterSave
{
    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * ReindexCategoryAfterSave constructor.
     *
     * @param IndexerRegistry $indexerRegistry The indexer registry
     */
    public function __construct(IndexerRegistry $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Reindex cms page's data into search engine after saving the cms page
     *
     * @param Page $subject The cms page being reindexed
     * @param Page $result  The parent function we are plugged on
     *
     * @return \Magento\Cms\Model\Page
     */
    public function afterSave(
        Page $subject,
        $result
    ) {
        if ($subject->getIsSearchable()) {
            $cmsPageIndexer = $this->indexerRegistry->get(Fulltext::INDEXER_ID);
            $cmsPageIndexer->reindexRow($subject->getId());
        }

        return $result;
    }
}
