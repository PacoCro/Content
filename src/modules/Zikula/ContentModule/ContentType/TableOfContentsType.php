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

use \Twig_Environment;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\ContentType\Form\Type\TableOfContentsType as FormType;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ThemeModule\Engine\Asset;

/**
 * Table of contents content type.
 */
class TableOfContentsType extends AbstractContentType
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var ContentDisplayHelper
     */
    protected $displayHelper;

    /**
     * @var boolean
     */
    protected $ignoreFirstTreeLevel;

    /**
     * TableOfContentsType constructor.
     *
     * @param TranslatorInterface  $translator           Translator service instance
     * @param Twig_Environment     $twig                 Twig service instance
     * @param FilesystemLoader     $twigLoader           Twig loader service instance
     * @param PermissionHelper     $permissionHelper     PermissionHelper service instance
     * @param Asset                $assetHelper          Asset service instance
     * @param Routerinterface      $router               Router service instance
     * @param EntityFactory        $entityFactory        EntityFactory service instance
     * @param ContentDisplayHelper $displayHelper        ContentDisplayHelper service instance
     * @param boolean              $ignoreFirstTreeLevel
     */
    public function __construct(
        TranslatorInterface $translator,
        Twig_Environment $twig,
        FilesystemLoader $twigLoader,
        PermissionHelper $permissionHelper,
        Asset $assetHelper,
        RouterInterface $router,
        EntityFactory $entityFactory,
        ContentDisplayHelper $displayHelper,
        $ignoreFirstTreeLevel = true
    ) {
        $this->router = $router;
        $this->entityFactory = $entityFactory;
        $this->displayHelper = $displayHelper;
        $this->ignoreFirstTreeLevel = $ignoreFirstTreeLevel;
        parent::__construct($translator, $twig, $twigLoader, $permissionHelper, $assetHelper);
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'book';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Table of contents');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('A table of contents of headings and subpages (built from the available Content pages).');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        $data = [
            'page' => 0,
            'includeSelf' => false,
            'includeNotInMenu' => false,
            'includeHeading' => 0, 
            'includeHeadingLevel' => 0,
            'includeSubpage' => 1,
            'includeSubpageLevel' => 0
        ];

        if (null !== $this->getEntity()) {
            $data['page'] = $this->getEntity()->getPage()->getId();
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function displayView()
    {
        // get the current active page where this contentitem is in
        $this->data['currentPage'] = $this->getEntity()->getPage();

        $this->data['toc'] = [];
        $pageId = $this->data['page'];
        if (!$pageId) {
            $pageId = 0;
        }

        $repository = $this->entityFactory->getRepository('page');
        $filters = [];

        if ($pageId == 0) {
            if (true === $this->ignoreFirstTreeLevel) {
                $filters[] = 'tbl.lvl = 1';
            } else {
                $filters[] = 'tbl.lvl = 0';
            }
        } else {
            $page = null;
            if ($pageId > 0) {
                $page = $repository->selectById($pageId);
                if (false === $page) {
                    return '';
                }
            }
            $filters[] = 'tbl.id = ' . $pageId;
        }
        if (!$this->data['includeNotInMenu']) {
            $filters[] = 'tbl.inMenu = 1';
        }

        $where = implode(' AND ', $filters);
        $useJoins = $this->data['includeHeading'] && $this->data['includeHeadingLevel'] > 0;

        $pages = $repository->selectWhere($where, 'tbl.lft', $useJoins);
        $this->data['toc']['toc'] = [];
        foreach ($pages as $page) {
            $this->data['toc']['toc'][] = $this->_genTocRecursive($page, ($pageId == 0 ? 1 : 0));
        }

        return parent::displayView();
    }

    protected function _genTocRecursive(PageEntity $page, $level)
    {
        $toc = [];
        $pageUrl = $this->router->generate('zikulacontentmodule_page_display', ['slug' => $page->getSlug()]);

        $includeHeadings = $this->data['includeHeading'] == 1 || ($this->data['includeHeading'] == 2 && $this->data['includeHeadingLevel'] - $level >= 0);
        if ($includeHeadings && count($page->getContentItems())) {
            foreach ($page->getContentItems() as $contentItem) {
                if ('Zikula\\ContentModule\\ContentType\\HeadingType' != $contentItem->getOwningType()) {
                    continue;
                }
                $contentType = $this->displayHelper->initContentType($contentItem);
                $output = $contentType->displayView();
                $headingData = $contentType->getData();
                $headingText = $headingData['text'];

                $toc[] = [
                    'title' => $headingText,
                    'url' => $pageUrl . '#heading_' . $contentItem->getId(),
                    'level' => $level,
                    'css' => 'content-toc-heading'
                ];
            }
        }

        $includeChildren = $this->data['includeSubpage'] == 1 || ($this->data['includeSubpage'] == 2 && $this->data['includeSubpageLevel'] - $level >= 0);
        if ($includeChildren && count($page->getChildren())) {
            foreach ($page->getChildren() as $subPage) {
                $toc[] = $this->_genTocRecursive($subPage, $level + 1);
            }
        }

        return [
            'pageId' => $page->getId(),
            'title' => $page->getTitle(),
            'url' => $pageUrl,
            'level' => $level,
            'css' => '',
            'toc' => $toc
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

        $assets['js'][] = $this->assetHelper->resolve('@ZikulaContentModule:js/ZikulaContentModule.ContentType.TableOfContents.js');

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

        return 'contentInitTocEdit';
    }
}
