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
     * @var \Smile\ElasticSuiteCms\Model\ResourceModel\Product\Indexer\Fulltext\Action\Full
     */
    private $resourceModel;

    /**
     * Constructor.
     *
     * @param ResourceModel $resourceModel Indexer resource model.
     */
    public function __construct(ResourceModel $resourceModel)
    {
        $this->resourceModel = $resourceModel;
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
                $lastCmsPageId = (int) $pageData['entity_id'];
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
}
