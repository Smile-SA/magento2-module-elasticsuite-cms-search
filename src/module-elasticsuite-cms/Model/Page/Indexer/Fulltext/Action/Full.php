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
namespace Smile\ElasticSuiteCms\Model\Page\Indexer\Fulltext\Action;

use Smile\ElasticSuiteCms\Model\ResourceModel\Page\Indexer\Fulltext\Action\Full as ResourceModel;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * ElasticSearch categories full indexer
 *
 * @category Smile
 * @package  Smile_ElasticSuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Full
{
    /**
     * @var \Smile\ElasticSuiteCms\Model\ResourceModel\Page\Indexer\Fulltext\Action\Full
     */
    private $resourceModel;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * Constructor.
     *
     * @param ResourceModel  $resourceModel  Indexer resource model.
     * @param FilterProvider $filterProvider Model template filter provider.
     */
    public function __construct(ResourceModel $resourceModel, FilterProvider $filterProvider)
    {
        $this->resourceModel  = $resourceModel;
        $this->filterProvider = $filterProvider;
    }

    /**
     * Get data for a list of cms in a store id.
     *
     * @param integer    $storeId    Store id.
     * @param array|null $cmsPageIds List of cms page ids.
     *
     * @return \Traversable
     */
    public function rebuildStoreIndex($storeId, $cmsPageIds = null)
    {
        $lastCmsPageId = 0;

        do {
            $cmsPages = $this->getSearchableCmsPage($storeId, $cmsPageIds, $lastCmsPageId);
            foreach ($cmsPages as $pageData) {
                $pageData = $this->processPageData($pageData);
                $lastCmsPageId = (int) $pageData['page_id'];
                yield $lastCmsPageId => $pageData;
            }
        } while (!empty($cmsPages));
    }

    /**
     * Load a bulk of cms page data.
     *
     * @param int     $storeId   Store id.
     * @param string  $cmsPageIds Cms page ids filter.
     * @param integer $fromId    Load product with id greater than.
     * @param integer $limit     Number of product to get loaded.
     *
     * @return array
     */
    private function getSearchableCmsPage($storeId, $cmsPageIds = null, $fromId = 0, $limit = 100)
    {
        return $this->resourceModel->getSearchableCmsPage($storeId, $cmsPageIds, $fromId, $limit);
    }

    /**
     * Parse template processor cms page content
     *
     * @param array $pageData Cms page data.
     *
     * @return array
     */
    protected function processPageData($pageData)
    {
        if (isset($pageData['content'])) {
            $pageData['content'] = $this->filterProvider->getPageFilter()->filter($pageData['content']);
        }
        return $pageData;
    }
}
