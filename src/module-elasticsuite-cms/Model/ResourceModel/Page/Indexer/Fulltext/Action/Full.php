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

namespace Smile\ElasticSuiteCms\Model\ResourceModel\Page\Indexer\Fulltext\Action;

use Smile\ElasticSuiteCatalog\Model\ResourceModel\Eav\Indexer\AbstractIndexer;

/**
 * ElasticSearch category full indexer resource model.
 *
 * @category  Smile
 * @package   Smile_ElasticSuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 */
class Full extends AbstractIndexer
{
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
    public function getSearchableCmsPage($storeId, $cmsPageIds = null, $fromId = 0, $limit = 100)
    {
        $select = $this->getConnection()->select()
            ->from(['p' => $this->getTable('cms_page')]);

        $this->addIsVisibleInStoreFilter($select, $storeId);

        if ($cmsPageIds !== null) {
            $select->where('p.page_id IN (?)', $cmsPageIds);
        }

        $select->where('p.page_id > ?', $fromId)
            ->limit($limit)
            ->order('p.page_id');
$logger = \Magento\Framework\App\ObjectManager::getInstance()->create('\Psr\Log\LoggerInterface');
$logger->debug('getSearchableCmsPage ');
        return $this->connection->fetchAll($select);

    }

    /**
     * Filter the select to append only cms page of current store.
     *
     * @param \Zend_Db_Select $select  Product select to be filtered.
     * @param integer         $storeId Store Id
     *
     * @return \Smile\ElasticSuiteCatalog\Model\ResourceModel\Product\Indexer\Fulltext\Action\Full Self Reference
     */
    private function addIsVisibleInStoreFilter($select, $storeId)
    {
        $select->join(
            ['ps' => $this->getTable('cms_page_store')],
            'p.page_id = ps.page_id'
        );
        $select->where('ps.store_id', $storeId);

        return $this;
    }
}
