<?php

declare(strict_types=1);

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\BreadcrumbType as FormType;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Breadcrumb content type.
 */
class BreadcrumbType extends AbstractContentType
{
    /**
     * @var bool
     */
    protected $ignoreFirstTreeLevel;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'sitemap';
    }

    public function getTitle(): string
    {
        return $this->__('Breadcrumb');
    }

    public function getDescription(): string
    {
        return $this->__('Show breadcrumbs for hierarchical pages.');
    }

    public function getDefaultData(): array
    {
        return [
            'includeSelf' => true, 
            'includeHome' => false
        ];
    }

    public function displayView(): string
    {
        $currentPage = $this->getEntity()->getPage();
        $this->data['currentPageId'] = $currentPage->getId();

        $pages = [];
        if (true === $this->data['includeSelf']) {
            $pages[] = $currentPage;
        }

        while (null !== $currentPage['parent']) {
            $currentPage = $currentPage['parent'];
            if (true !== $this->ignoreFirstTreeLevel || $currentPage->getLvl() > 0) {
                array_unshift($pages, $currentPage);
            }
        }

        $this->data['pages'] = $pages;

        return parent::displayView();
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    /**
     * @required
     */
    public function setIgnoreFirstTreeLevel(VariableApiInterface $variableApi): void
    {
        $this->ignoreFirstTreeLevel = (bool)$variableApi->get('ZikulaContentModule', 'ignoreFirstTreeLevelInRoutes', true);
    }
}
