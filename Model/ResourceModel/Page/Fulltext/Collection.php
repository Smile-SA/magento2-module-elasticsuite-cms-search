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
namespace Smile\ElasticsuiteCms\Model\ResourceModel\Page\Fulltext;

use Smile\ElasticsuiteCore\Search\RequestInterface;
use Smile\ElasticsuiteCore\Search\Request\BucketInterface;
use Magento\Framework\DB\Select;

/**
 * Search engine product collection.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 */
class Collection extends \Magento\Cms\Model\ResourceModel\Page\Collection
{
    /**
     * @var QueryResponse
     */
    private $queryResponse;

    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\Builder
     */
    private $requestBuilder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    private $searchEngine;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var array
     */
    private $facets = [];

    /**
     * @var integer
     */
    private $storeId;

    /**
     * Constructor.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory     Collection entity factory
     * @param \Psr\Log\LoggerInterface                                     $logger            Logger.
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy     Db Fetch strategy.
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager      Event manager.
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager      Store manager.
     * @param \Magento\Framework\EntityManager\MetadataPool                $metadataPool      Metadata pool.
     * @param \Smile\ElasticsuiteCore\Search\Request\Builder               $requestBuilder    Search request
     *                                                                                        builder.
     * @param \Magento\Search\Model\SearchEngine                           $searchEngine      Search engine
     * @param string                                                       $searchRequestName Search request
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection        Db Connection.
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource          DB connection.
     *
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Smile\ElasticsuiteCore\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        $searchRequestName = 'cms_search_container',
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $storeManager, $metadataPool, $connection, $resource);

        $this->requestBuilder    = $requestBuilder;
        $this->searchEngine      = $searchEngine;
        $this->searchRequestName = $searchRequestName;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        if ($this->_totalRecords === null) {
            $this->loadCmsPageCounts();
        }

        return $this->_totalRecords;
    }

    /**
     * {@inheritDoc}
     */
    public function setOrder($attribute, $dir = \Magento\Framework\DB\Select::SQL_DESC)
    {
        throw new \LogicException("Sorting on multiple stores is not allowed in search engine collections.");
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $storeId Store id
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * Returns current store id.
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Add filter by store
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag) Method is inherited
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Method is inherited
     *
     * @param int|array|\Magento\Store\Model\Store $store     Store
     * @param bool                                 $withAdmin With admin
     *
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (is_object($store)) {
            $store = $store->getId();
        }

        if (is_array($store)) {
            throw new \LogicException("Filtering on multiple stores is not allowed in search engine collections.");
        }

        return $this->setStoreId($store);
    }

    /**
     * Add search query filter
     *
     * @param string $query Search query text.
     *
     * @return \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection
     */
    public function addSearchFilter($query)
    {
        $this->queryText = $query;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $this->filters[$field] = $condition;

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $searchRequest = $this->prepareRequest();

        $this->queryResponse = $this->searchEngine->search($searchRequest);

        // Update the product count.
        $this->_totalRecords = $this->queryResponse->count();

        // Filter search results. The pagination has to be resetted since it is managed by the engine itself.
        $docIds = array_map(
            function (\Magento\Framework\Api\Search\Document $doc) {
                return (int) $doc->getId();
            },
            $this->queryResponse->getIterator()->getArrayCopy()
        );

        if (empty($docIds)) {
            $docIds[] = 0;
        }

        $this->getSelect()->where('main_table.page_id IN (?)', ['in' => $docIds]);
        $this->_pageSize = false;

        return parent::_renderFiltersBefore();
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * {@inheritDoc}
     */
    protected function _afterLoad()
    {
        // Resort items according the search response.
        $orginalItems = $this->_items;
        $this->_items = [];

        foreach ($this->queryResponse->getIterator() as $document) {
            $documentId = $document->getId();
            if (isset($orginalItems[$documentId])) {
                $this->_items[$documentId] = $orginalItems[$documentId];
                $this->_items[$documentId]->setStoreId($this->storeId);
            }
        }

        return parent::_afterLoad();
    }

    /**
     * Prepare the search request before it will be executed.
     *
     * @return RequestInterface
     */
    private function prepareRequest()
    {
        // Store id and request name.
        $storeId           = $this->storeId;
        $searchRequestName = $this->searchRequestName;

        // Pagination params.
        $size = $this->_pageSize ? $this->_pageSize : 20;
        $from = $size * (max(1, $this->_curPage) - 1);

        // Query text.
        $queryText = $this->queryText;

        // Setup sort orders.
        $sortOrders = $this->prepareSortOrders();

        $searchRequest = $this->requestBuilder->create(
            $storeId,
            $searchRequestName,
            $from,
            $size,
            $queryText,
            $sortOrders,
            $this->filters,
            $this->facets
        );

        return $searchRequest;
    }

    /**
     * Prepare sort orders for the request builder.
     *
     * @return array()
     */
    private function prepareSortOrders()
    {
        $sortOrders = [];

        foreach ($this->_orders as $attribute => $direction) {
            $sortParams = ['direction' => $direction];
            $sortField = $this->mapFieldName($attribute);
            $sortOrders[$sortField] = $sortParams;
        }

        return $sortOrders;
    }

    /**
     * Convert standard field name to ES fieldname.
     * (eg. category_ids => category.category_id).
     *
     * @param string $fieldName Field name to be mapped.
     *
     * @return string
     */
    private function mapFieldName($fieldName)
    {
        if (isset($this->fieldNameMapping[$fieldName])) {
            $fieldName = $this->fieldNameMapping[$fieldName];
        }

        return $fieldName;
    }

    /**
     * Load cms page collection size.
     *
     * @return void
     */
    private function loadCmsPageCounts()
    {
        $storeId     = $this->getStoreId();
        $requestName = $this->searchRequestName;

        // Query text.
        $queryText = $this->queryText;

        $searchRequest = $this->requestBuilder->create($storeId, $requestName, 0, 0, $queryText, [], $this->filters);

        $searchResponse = $this->searchEngine->search($searchRequest);

        $this->_totalRecords = $searchResponse->count();
    }
}
