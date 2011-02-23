<?php
/**
 * Content BreadCrumb Plugin
 *
 * @copyright (C) 2010, Sven Strickroth, TU Clausthal
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_Breadcrumb extends Content_ContentType
{
    var $pageid;
    function  __construct(array $data = array()) {
        parent::__construct();
        $this->pageid = isset($data['pageId']) ? $data['pageId'] : null;
    }
    function getTitle()
    {
        return $this->__('BreadCrumb');
    }
    function getDescription()
    {
        return $this->__('Show breadcrumbs for hierarchical pages');
    }
    function isTranslatable()
    {
        return false;
    }
    function display()
    {
        $path = array();
        $pageid = $this->pageid;
        while ($pageid > 0) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array(
                'id' => $pageid,
                'includeContent' => false,
                'translate' => false));
            array_unshift($path, $page);
            $pageid = $page['parentPageId'];
        }

        $view = Zikula_View::getInstance('Content', false);
        $view->assign('thispage', $this->pageid);
        $view->assign('path', $path);

        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        return '';
    }
    function getDefaultData()
    {
        return array();
    }
}