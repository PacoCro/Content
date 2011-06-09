<?php

/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */
function smarty_function_contenthtmleditor($params, &$view)
{
    $dom = ZLanguage::getModuleDomain('Content');
    $inputId = $params['inputId'];
    $inputType = isset($params['inputType']) ? $params['inputType'] : null;

    // Get reference to optional radio button that enables the editor (a hack for the HTML plugin).
    // It would have been easier just to read a $var in the template, but this won't work with a
    // Forms plugin - you would just get the initial value from when the page was loaded
    $htmlRadioButton = (isset($params['htmlradioid']) ? $view->getPluginById($params['htmlradioid']) : null);
    $textRadioButton = (isset($params['textradioid']) ? $view->getPluginById($params['textradioid']) : null);

    $html = '';

    $useWysiwyg = $htmlRadioButton == null && $inputType == null && !$view->isPostBack()
            || $htmlRadioButton == null && $inputType == 'html' && !$view->isPostBack()
            || $htmlRadioButton != null && $htmlRadioButton->checked;

    $useBBCode = $textRadioButton == null && $inputType == 'text' && !$view->isPostBack()
            || $textRadioButton != null && $textRadioButton->checked;

    if ($useWysiwyg && ModUtil::available('Scribite'))
    {
        $scribite = ModUtil::apiFunc('Scribite', 'User', 'loader', array(
                    'modulename' => 'Content',
                    'areas' => array($inputId)));
        PageUtil::AddVar('header', $scribite);
    } else if ($useWysiwyg && !ModUtil::available('Scribite')) {
        $html = "<div class=\"z-formrow\"><em class=\"z-sub\">";
        $html .= '(' . __("Please install the Scribite module to use the javascript HTML editor.", $dom) . ')';
        $html .= "</em></div>";
    } else if ($useBBCode && ModUtil::available('BBCode')) {
        $html = "<div class=\"z-formrow\"><em class=\"z-sub\">";
        $html .= ModUtil::func('BBCode', 'User', 'bbcodes', array(
                    'textfieldid' => $inputId,
                    'images' => 0));
        $html .= "</em></div>";
    } else if ($useBBCode && !ModUtil::available('BBCode')) {
        $html = "<div class=\"z-formrow\"><em class=\"z-sub\">";
        $html .= '(' . __("Please install the BBCode module to enable BBCodes display.", $dom) . ')';
        $html .= "</em></div>";
    }

    return $html;
}
