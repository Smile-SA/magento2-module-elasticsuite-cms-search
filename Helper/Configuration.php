<?php
/**
 * DISCLAIMER :
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

namespace Smile\ElasticsuiteCms\Helper;

use Smile\ElasticsuiteCore\Helper\AbstractConfiguration;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Smile_ElasticsuiteCore search engine configuration default implementation.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCms
 * @author    Fanny DECLERCK <fadec@smile.fr>
 */
class Configuration extends AbstractConfiguration
{
    /**
     * Location of Elasticsuite cms page settings configuration.
     *
     * @var string
     */
    const CONFIG_XML_PREFIX = 'smile_elasticsuite_cms/cms_settings';

    /**
     * Retrieve a configuration value by its key
     *
     * @param string $key The configuration key
     *
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue(self::CONFIG_XML_PREFIX . "/" . $key);
    }
}
