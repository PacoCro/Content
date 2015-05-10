<?php
/**
 * Content 3 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_LayoutType_Column3d255025 extends Content_AbstractLayoutType
{
    protected $templateType = 0;

    function __construct(Zikula_View $view)
    {
        parent::__construct($view);
        $this->contentAreaTitles = array(
            $this->__('Header'),
            $this->__('Left column'),
            $this->__('Centre column'),
            $this->__('Right column'),
            $this->__('Footer'));
    }
    function getTitle()
    {
        return $this->__('3 columns (25|50|25)');
    }
    function getDescription()
    {
        return $this->__('Header + three columns (25|50|25) + footer');
    }
    function getNumberOfContentAreas()
    {
        return 5;
    }
    function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layouttype/column3_255025_header.png';
    }
}