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
namespace Smile\ElasticSuiteCms\Controller\Result;

/**
 * Cms page results.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @category Smile
 * @package  Smile_ElasticSuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * PHP Constructor
     *
     * @param \Magento\Framework\App\Action\Context      $context     Action context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory Page Factory
     *
     * @return Index
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $pageFactory;
    }

    /**
     * Execute the action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
