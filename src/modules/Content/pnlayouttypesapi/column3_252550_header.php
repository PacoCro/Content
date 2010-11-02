<?php
/**
 * Content 3 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: column3header.php 425 2010-06-28 14:32:52Z philipp $
 * @license See license.txt
 */

class content_layouttypesapi_column3_252550_headerPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $this->contentAreaTitles = array(__('Header', $dom), __('Left column', $dom), __('Centre column', $dom), __('Right column', $dom), __('Footer', $dom));
    }
    function content_layouttypesapi_column3_252550_headerPlugin()
    {
        $this->__construct();
    }
    function getName()
    {
        return 'column3_252550_header';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('3 columns (25|25|50)', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Header + three columns (25|25|50) + footer', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 5;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
    function getImage()
    {
    	return pngetBaseUrl().'/modules/content/pnimages/layout/column3_252550_header.png';
    }
}

function content_layouttypesapi_column3_252550_header($args)
{
    return new content_layouttypesapi_column3_252550_headerPlugin();
}
