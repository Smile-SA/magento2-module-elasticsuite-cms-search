## ElasticSuite CMS Pages Search

This module is a plugin for [ElasticSuite](https://github.com/Smile-SA/elasticsuite).

It allows to index CMS Pages into the search engine and display them into the autocomplete results, and also on the search result page.

### ⚠️ Magento versions compatibility :

**Which version should I use ?**

Magento Version                                     | Module Version
----------------------------------------------------|------------------------------------------------------------------------
Magento **2.0.x** Opensource (CE) / Commerce (EE)   |**2.0.x** latest release : ```composer require smile/module-elasticsuite-cms-search ~2.0.0```
Magento **2.1.x** Opensource (CE) / Commerce (EE)   |**2.1.x** latest release : ```composer require smile/module-elasticsuite-cms-search ~2.1.0```
Magento **2.2.x** Opensource (CE) / Commerce (EE)   |**2.1.x** latest release : ```composer require smile/module-elasticsuite-cms-search ~2.1.0```

### Requirements

The module requires :

- [ElasticSuite](https://github.com/Smile-SA/elasticsuite) > 2.1.*

### How to use

1. Install the module via Composer :

``` composer require smile/module-elasticsuite-cms-search ```

2. Enable it

``` bin/magento module:enable Smile_ElasticsuiteCms ```

3. Install the module and rebuild the DI cache

``` bin/magento setup:upgrade ```

4. Process a full reindex of the CMS Page search index

``` bin/magento index:reindex elasticsuite_cms_page_fulltext ```


### How to configure

> Stores > Configuration > Elasticsuite > CMS settings > Settings
* Max result : Maximum number of results to display in result block.

> Stores > Configuration > Elasticsuite > Autocomplete > Cms page Autocomplete
* Max size : Maximum number of cms pages to display in autocomplete results.

### Fields indexed

Field               | Type    
--------------------|-----------
page_id             | Integer
title               | Varchar
page_layout         | Varchar
meta_keywords       | Text
meta_description    | Text
identifier          | Integer
content_heading     | Text
content             | Text
creation_time       | DateTime
update_time         | DateTime
is_active           | Integer
sort_order          | Integer
layout_update_xml   | Text
custom_theme        | Integer
custom_root_template| Integer
custom_layout_update| Text
custom_theme_from   | DateTime
custom_theme_to     | DateTime
meta_title          | Text
is_searchable       | Integer
store_id            | Integer 
   
Index example :
```  
{
    "_index" : "magento2_fr_cms_page_20181024_064926",
    "_type" : "page",
    "_id" : "5",
    "_score" : 1.0,
    "_source" : {
      "page_id" : "5",
      "title" : "About us",
      "page_layout" : "1column",
      "meta_keywords" : "",
      "meta_description" : "",
      "identifier" : "about-us",
      "content_heading" : "About us",
      "content" : "<div class=\"about-info cms-content\">\n      <p class=\"cms-content-important\">With more than 230 stores spanning 43 states and growing, Luma is a nationally recognized active wear manufacturer and retailer. We’re passionate about active lifestyles – and it goes way beyond apparel.</p>\n\n >\n",
      "creation_time" : "2017-03-21 16:59:21",
      "update_time" : "2018-10-24 06:45:28",
      "is_active" : "1",
      "sort_order" : "0",
      "layout_update_xml" : "",
      "custom_theme" : null,
      "custom_root_template" : null,
      "custom_layout_update_xml" : "",
      "custom_theme_from" : null,
      "custom_theme_to" : null,
      "meta_title" : "",
      "is_searchable" : "1",
      "store_id" : "0"
    }
}
```