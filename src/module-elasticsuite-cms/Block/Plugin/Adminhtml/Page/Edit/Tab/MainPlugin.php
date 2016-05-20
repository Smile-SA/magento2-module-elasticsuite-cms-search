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
namespace Smile\ElasticSuiteCms\Block\Plugin\Adminhtml\Page\Edit\Tab;

use Magento\Cms\Block\Adminhtml\Page\Edit\Tab\Main;
use Magento\Framework\Data\Form;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Registry;

/**
 * Plugin that happend custom fields dedicated to search configuration
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @category Smile
 * @package  Smile_ElasticSuiteCms
 * @author   Fanny DECLERCK <fadec@smile.fr>
 */
class MainPlugin
{

    /**
     * @var Yesno
     */
    private $booleanSource;

    /**
     * @var core registry
     */
    private $coreRegistry;

    /**
     * Class constructor
     *
     * @param Yesno    $booleanSource The YesNo source.
     * @param Registry $registry      Core registry.
     */
    public function __construct(Yesno $booleanSource, Registry $registry)
    {
        $this->booleanSource = $booleanSource;
        $this->coreRegistry  = $registry;
    }

    /**
     * Append ES specifics fields into the attribute edit store front tab.
     *
     * @param Main  $subject The StoreFront tab
     * @param \Closure $proceed The parent function
     * @param Form     $form    The form
     *
     * @return Main
     */
    public function aroundSetForm(Main $subject, \Closure $proceed, Form $form)
    {
        $block = $proceed($form);

        $fieldset = $this->createFieldset($form);
        $fieldset->addField(
            'is_searchable',
            'select',
            [
                'label' => __('Is searchable'),
                'title' => __('Is searchable'),
                'name'  => 'is_searchable',
                'required' => true,
                'options' => $this->booleanSource->toArray(),
            ],
            'is_active'
        );

        $model = $this->coreRegistry->registry('cms_page');
        $form->setValues($model->getData());

        return $block;
    }

    /**
     * Append the "Search Configuration" fieldset to the tab.
     *
     * @param Form $form Target form.
     *
     * @return Fieldset
     */
    private function createFieldset(Form $form)
    {
        $fieldset = $form->addFieldset(
            'elasticsuite_cms_attribute_fieldset',
            [
                'legend' => __('Search Configuration'),
            ],
            'content_fieldset'
        );

        return $fieldset;
    }
}