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
namespace Smile\ElasticsuiteCms\Block\Page;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Search\Model\QueryFactory;
use Smile\ElasticsuiteCms\Model\ResourceModel\Page\Fulltext\CollectionFactory as PageCollectionFactory;

/**
 * Plugin that happend custom fields dedicated to search configuration
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Result extends \Magento\Framework\View\Element\Template
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Smile\ElasticsuiteCms\Model\ResourceModel\Page\Fulltext\Collection
     */
    private $pageCollection;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $cmsPage;

    /**
     * Suggest constructor.
     *
     * @param TemplateContext          $context               Template contexte.
     * @param QueryFactory             $queryFactory          Query factory.
     * @param PageCollectionFactory    $pageCollectionFactory Page collection factory.
     * @param \Magento\Cms\Helper\Page $cmsPage               Cms helper page.
     * @param array                    $data                  Data.
     */
    public function __construct(
        TemplateContext $context,
        QueryFactory $queryFactory,
        PageCollectionFactory $pageCollectionFactory,
        \Magento\Cms\Helper\Page $cmsPage,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->queryFactory   = $queryFactory;
        $this->pageCollection = $this->initPageCollection($pageCollectionFactory);
        $this->cmsPage        = $cmsPage;
    }

    /**
     * Returns cms page collection.
     *
     * @return \Smile\ElasticsuiteCms\Model\ResourceModel\Page\Fulltext\Collection
     */
    public function getPageCollection()
    {
        return $this->pageCollection;
    }

    /**
     * Returns collection size.
     *
     * @return int|null
     */
    public function getResultCount()
    {
        return $this->getPageCollection()->getSize();
    }

    /**
     * Returns query.
     *
     * @return \Magento\Search\Model\Query
     */
    public function getQuery()
    {
        return $this->queryFactory->get();
    }

    /**
     * Returns query text.
     *
     * @return string
     */
    public function getQueryText()
    {
        return $this->getQuery()->getQueryText();
    }

    /**
     * Returns page url.
     *
     * @param int $pageId Page id
     *
     * @return mixed
     */
    public function getPageUrl($pageId)
    {
        return $this->cmsPage->getPageUrl($pageId);
    }

    /**
     * Init cms page collection.
     *
     * @param PageCollectionFactory $collectionFactory Cms page collection.
     *
     * @return mixed
     */
    private function initPageCollection($collectionFactory)
    {
        $pageCollection = $collectionFactory->create();

        $queryText = $this->getQueryText();
        $pageCollection->addSearchFilter($queryText);

        return $pageCollection;
    }
}
