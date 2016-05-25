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
namespace Smile\ElasticSuiteCms\Block\Page;

use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Search\Model\QueryFactory;
use Smile\ElasticSuiteCms\Model\ResourceModel\Page\Fulltext\CollectionFactory as PageCollectionFactory;
use Smile\ElasticSuiteCms\Helper\Configuration;

/**
 * Plugin that happend custom fields dedicated to search configuration
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @category Smile
 * @package  Smile_ElasticSuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Suggest extends \Magento\Framework\View\Element\Template
{
    /**
     * Name of field to get max results.
     *
     * @var string
     */
    const MAX_RESULT = 'max_result';

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Configuration
     */
    private $helper;

    /**
     * @var \Smile\ElasticSuiteCms\Model\ResourceModel\Page\Fulltext\Collection
     */
    private $pageCollection;

    /**
     * Suggest constructor.
     *
     * @param TemplateContext       $context               Template contexte.
     * @param QueryFactory          $queryFactory          Query factory.
     * @param PageCollectionFactory $pageCollectionFactory Page collection factory.
     * @param Configuration         $helper                Configuration helper.
     * @param array                 $data                  Data.
     */
    public function __construct(TemplateContext $context,
        QueryFactory $queryFactory,
        PageCollectionFactory $pageCollectionFactory,
        Configuration $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->queryFactory   = $queryFactory;
        $this->helper         = $helper;
        $this->pageCollection = $this->initPageCollection($pageCollectionFactory);
    }

    /**
     * Returns if block can be display.
     * 
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->getResultCount() > 0;
    }

    /**
     * Returns cms page collection.
     *
     * @return \Smile\ElasticSuiteCms\Model\ResourceModel\Page\Fulltext\Collection
     */
    public function getPageCollection()
    {
        return $this->pageCollection;
    }

    /**
     * Returns number of results.
     *
     * @return int
     */
    public function getNumberOfResults()
    {
        return $this->helper->getConfigValue(self::MAX_RESULT);
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
     * Init cms page collection.
     *
     * @param PageCollectionFactory $collectionFactory Cms page collection.
     *
     * @return mixed
     */
    private function initPageCollection($collectionFactory)
    {
        $pageCollection = $collectionFactory->create();

        $pageCollection->setPageSize($this->getNumberOfResults());

        $queryText = $this->getQueryText();
        $pageCollection->addSearchFilter($queryText);

        return $pageCollection;
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
}