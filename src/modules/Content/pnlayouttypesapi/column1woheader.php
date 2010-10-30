<?php
/**
 * Content 1 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_layouttypesapi_column1woheaderPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $this->contentAreaTitles = array(__('Centre column', $dom), __('Footer', $dom));
    }
    function content_layouttypesapi_column1woheaderPlugin()
    {
        $this->__construct();
    }
    function getName()
    {
        return 'column1woheader';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('1 column no header', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Single 100% wide column without header', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 1;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
    function getImage()
    {
    	return pngetBaseUrl().'/modules/Content/pnimages/layout/column1woheader.png';
    }
}

function content_layouttypesapi_column1woheader($args)
{
    return new content_layouttypesapi_column1woheaderPlugin();
}
