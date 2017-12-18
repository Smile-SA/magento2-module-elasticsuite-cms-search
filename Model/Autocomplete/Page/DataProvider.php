<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\ElasticsuiteCms\Model\Autocomplete\Page;

use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ElasticsuiteCore\Helper\Autocomplete as ConfigurationHelper;
use Smile\ElasticsuiteCms\Model\ResourceModel\Page\Fulltext\CollectionFactory as CmsCollectionFactory;
use Smile\ElasticsuiteCore\Model\Autocomplete\Terms\DataProvider as TermDataProvider;

/**
 * Catalog product autocomplete data provider.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class DataProvider implements DataProviderInterface
{
    /**
     * Autocomplete type
     */
    const AUTOCOMPLETE_TYPE = "cms_page";

    /**
     * Autocomplete result item factory
     *
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * Query factory
     *
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var TermDataProvider
     */
    protected $termDataProvider;

    /**
     * @var CmsCollectionFactory
     */
    protected $cmsCollectionFactory;

    /**
     * @var ConfigurationHelper
     */
    protected $configurationHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string Autocomplete result type
     */
    private $type;

    /**
     * Constructor.
     *
     * @param ItemFactory           $itemFactory          Suggest item factory.
     * @param QueryFactory          $queryFactory         Search query factory.
     * @param TermDataProvider      $termDataProvider     Search terms suggester.
     * @param CmsCollectionFactory  $cmsCollectionFactory Cms collection factory.
     * @param ConfigurationHelper   $configurationHelper  Autocomplete configuration helper.
     * @param StoreManagerInterface $storeManager         Store manager.
     * @param string                $type                 Autocomplete provider type.
     */
    public function __construct(
        ItemFactory $itemFactory,
        QueryFactory $queryFactory,
        TermDataProvider $termDataProvider,
        CmsCollectionFactory $cmsCollectionFactory,
        ConfigurationHelper $configurationHelper,
        StoreManagerInterface $storeManager,
        $type = self::AUTOCOMPLETE_TYPE
    ) {
        $this->itemFactory          = $itemFactory;
        $this->queryFactory         = $queryFactory;
        $this->termDataProvider     = $termDataProvider;
        $this->cmsCollectionFactory = $cmsCollectionFactory;
        $this->configurationHelper  = $configurationHelper;
        $this->type                 = $type;
        $this->storeManager         = $storeManager;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        $result = [];
        $pageCollection = $this->getCmsPageCollection();
        if ($pageCollection) {
            foreach ($pageCollection as $page) {
                $result[] = $result, $this->itemFactory->create([
                    'title' => $page->getTitle(),
                    'url'   => $this->storeManager->getStore()->getBaseUrl(). $page->getIdentifier(),
                    'type'  => $this->getType(),]);
	    }
        }

        return $result;
    }

    /**
     * List of search terms suggested by the search terms data daprovider.
     *
     * @return array
     */
    private function getSuggestedTerms()
    {
        $terms = array_map(
            function (\Magento\Search\Model\Autocomplete\Item $termItem) {
                return $termItem->getTitle();
            },
            $this->termDataProvider->getItems()
        );

        return $terms;
    }

    /**
     * Suggested pages collection.
     * Returns null if no suggested search terms.
     *
     * @return \Smile\ElasticsuiteCms\Model\ResourceModel\Page\Fulltext\Collection|null
     */
    private function getCmsPageCollection()
    {
        $pageCollection = null;
        $suggestedTerms = $this->getSuggestedTerms();
        $terms          = [$this->queryFactory->get()->getQueryText()];

        if (!empty($suggestedTerms)) {
            $terms = array_merge($terms, $suggestedTerms);
        }

        $pageCollection = $this->cmsCollectionFactory->create();
        $pageCollection->addSearchFilter($terms);
        $pageCollection->setPageSize($this->getResultsPageSize());

        return $pageCollection;
    }

    /**
     * Retrieve number of pages to display in autocomplete results
     *
     * @return int
     */
    private function getResultsPageSize()
    {
        return $this->configurationHelper->getMaxSize($this->getType());
    }
}
