<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

use Zikula\ContentModule\AbstractContentType;
use Zikula\ContentModule\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\UnfilteredType as FormType;

/**
 * Unfiltered raw content type.
 */
class UnfilteredType extends AbstractContentType
{
    /**
     * @var boolean
     */
    protected $enableRawPlugin;

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_EXPERT;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'user-secret';
    }

    /**
     * @inheritDoc
     */
    function getTitle()
    {
        return $this->__('Unfiltered raw text');
    }

    /**
     * @inheritDoc
     */
    function getDescription()
    {
        return $this->__('A plugin for unfiltered raw output (iframes, JavaScript, banners, etc).');
    }

    /**
     * @inheritDoc
     */
    public function getAdminInfo()
    {
        return $this->__('You need to explicitly enable a checkbox in the configuration form to activate this plugin.');
    }

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        // Only active when the admin has enabled this plugin
        return $this->enableRawPlugin && parent::isActive();
    }

    /**
     * @inheritDoc
     */
    function isTranslatable()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'text' => $this->__('Add unfiltered text here ...'),
            'useiframe' => false,
            'iframeSrc' => '',
            'iframeName' => '',
            'iframeTitle' => '',
            'iframeStyle' => 'border:0',
            'iframeWidth' => 800,
            'iframeHeight' => 600,
            'iframeBorder' => 0,
            'iframeScrolling' => 'no',
            'iframeAllowTransparancy' => true
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return FormType::class;
    }

    /**
     * @inheritDoc
     */
    public function getAssets($context)
    {
        $assets = parent::getAssets($context);
        if (ContentTypeInterface::CONTEXT_EDIT != $context) {
            return $assets;
        }

        $assets['js'][] = $this->assetHelper->resolve('@ZikulaContentModule:js/ZikulaContentModule.ContentType.Unfiltered.js');

        return $assets;
    }

    /**
     * @inheritDoc
     */
    public function getJsEntrypoint($context)
    {
        if (ContentTypeInterface::CONTEXT_EDIT != $context) {
            return null;
        }

        return 'contentInitUnfilteredEdit';
    }

    /**
     * @param boolean $enableRawPlugin
     */
    public function setEnableRawPlugin($enableRawPlugin)
    {
        $this->enableRawPlugin = $enableRawPlugin;
    }
}
