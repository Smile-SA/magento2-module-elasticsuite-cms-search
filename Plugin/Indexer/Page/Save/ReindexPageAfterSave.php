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
     * Reindex cms page's data into search engine after saving the cms page.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Cms\Model\ResourceModel\Page                $subject The resource model.
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $result  Result of save() method.
     * @param \Magento\Framework\Model\AbstractModel               $page    The CMS page being reindexed.
     *
     * @return \Magento\Cms\Model\Page
     */
    public function afterSave(
        \Magento\Cms\Model\ResourceModel\Page $subject,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $result,
        \Magento\Framework\Model\AbstractModel $page
    ) {
        $isSearchable  = $page->getIsSearchable();
        $wasSearchable = ($page->dataHasChangedFor('is_searchable') && (int) $page->getOrigData('is_searchable') === 1);
        if ($isSearchable || $wasSearchable) {
            $cmsPageIndexer = $this->indexerRegistry->get(Fulltext::INDEXER_ID);
            if (!$cmsPageIndexer->isScheduled()) {
                $cmsPageIndexer->reindexRow($page->getId());
            }
        }

        return $result;
    }
}
