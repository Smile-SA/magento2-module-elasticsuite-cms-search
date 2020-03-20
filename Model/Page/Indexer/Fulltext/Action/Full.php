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
namespace Smile\ElasticsuiteCms\Model\Page\Indexer\Fulltext\Action;

use Magento\Framework\Filter\RemoveTags;
use Smile\ElasticsuiteCms\Model\ResourceModel\Page\Indexer\Fulltext\Action\Full as ResourceModel;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Store\Model\App\Emulation;

/**
 * ElasticSearch CMS Pages full indexer
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Full
{
    /**
     * @var \Smile\ElasticsuiteCms\Model\ResourceModel\Page\Indexer\Fulltext\Action\Full
     */
    private $resourceModel;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @var \Magento\Framework\App\AreaList
     */
    private $areaList;

    /**
     * @var \Magento\Framework\Filter\RemoveTags
     */
    private $stripTags;

    /**
     * Constructor.
     *
     * @param ResourceModel  $resourceModel  Indexer resource model.
     * @param FilterProvider $filterProvider Model template filter provider.
     * @param AreaList       $areaList       Area List
     * @param RemoveTags     $stripTags      HTML Tags remover
     */
    public function __construct(
        ResourceModel $resourceModel,
        FilterProvider $filterProvider,
        AreaList $areaList,
        RemoveTags $stripTags
    ) {
        $this->resourceModel  = $resourceModel;
        $this->filterProvider = $filterProvider;
        $this->areaList       = $areaList;
        $this->stripTags      = $stripTags;
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

        try {
            $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);
        } catch (\InvalidArgumentException | \LogicException $exception) {
            // Can occur especially when magento sample data are triggering a full reindex.
            ;
        }

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
     * @param int     $storeId    Store id.
     * @param string  $cmsPageIds Cms page ids filter.
     * @param integer $fromId     Load product with id greater than.
     * @param integer $limit      Number of product to get loaded.
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
    private function processPageData($pageData)
    {
        if (isset($pageData['content'])) {
            $content = html_entity_decode($this->filterProvider->getPageFilter()->filter($pageData['content']));
            $content = $this->stripTags->filter($content);
            $content = preg_replace('/\s\s+/', ' ', $content);
            $pageData['content'] = $content;
        }

        return $pageData;
    }
}
