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

namespace Zikula\ContentModule\Helper;

use Symfony\Component\Routing\RouterInterface;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\PageCategoryEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Helper\Base\AbstractExampleDataHelper;

/**
 * Example data helper implementation class.
 */
class ExampleDataHelper extends AbstractExampleDataHelper
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ContentDisplayHelper
     */
    protected $displayHelper;

    /**
     * @inheritDoc
     */
    public function createDefaultData()
    {
        // example category
        $categoryId = 41; // Business and work
        $category = $this->entityFactory->getObjectManager()->find('ZikulaCategoriesModule:CategoryEntity', $categoryId);

        // determine category registry identifiers
        $registryRepository = $this->entityFactory->getObjectManager()->getRepository('ZikulaCategoriesModule:CategoryRegistryEntity');
        $categoryRegistries = $registryRepository->findBy(['modname' => 'ZikulaContentModule']);
        $categoryRegistry = null;
        foreach ($categoryRegistries as $registry) {
            if ($registry->getEntityname() == 'PageEntity') {
                $categoryRegistry = $registry;
                break;
            }
        }

        $contentTypeNamespace = 'Zikula\\ContentModule\\ContentType\\';
        $itemHeightEditing = 3;

        $mainPage = new PageEntity();
        $mainPage->setTitle($this->translator->__('Pages', 'zikulacontentmodule'));
        $mainPage->setLayout([]);
        $mainPage->setActive(true);
        $mainPage->setInMenu(true);
        $mainPage->setParent(null);
        $mainPage->setRoot(1);

        $mainContentInfo = [];
        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'HeadingType');
        $item->setContentData([
            'text' => $this->translator->__('This is the main page', 'zikulacontentmodule')
        ]);
        $mainContentInfo[] = [$item, 'header', ['x' => 0, 'y' => (0 * $itemHeightEditing), 'width' => 12, 'minWidth' => 2]];

        $page = new PageEntity();
        $page->setTitle($this->translator->__('Content introduction page', 'zikulacontentmodule'));
        $page->setLayout([]);
        $page->setActive(true);
        $page->setInMenu(true);
        $page->setParent($mainPage);
        $page->setRoot(1);
        $page->getCategories()->add(new PageCategoryEntity($categoryRegistry->getId(), $category, $page));

        $contentInfo = [];

        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'HeadingType');
        $item->setContentData([
            'text' => $this->translator->__('A content page consists of various content items in a chosen Bootstrap grid', 'zikulacontentmodule')
        ]);
        $contentType = $this->displayHelper->initContentType($item);
        $item->setSearchText($contentType->getSearchableText());
        $item->setAdditionalSearchText($this->translator->__('Content pages may arrange content items using Bootstrap layout.', 'zikulacontentmodule'));
        $contentInfo[] = [$item, 'header', ['x' => 0, 'y' => (0 * $itemHeightEditing), 'width' => 12, 'minWidth' => 2]];

        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'HtmlType');
        $item->setContentData([
            'text' => $this->translator->__('<p>Each created page can arrange their content elements using arbitrary grid layouts. Each page may contains various layout sections. In each section you can place one or more content items of various kinds like:</p> <ul> <li>HTML text;</li> <li>YouTube videos;</li> <li>Google maps;</li> <li>Quotes;</li> <li>Atom or RSS feeds;</li> <li>Computer code;</li> <li>Zikula blocks;</li> <li>the output of another Zikula module or Symfony bundle.</li> </ul> <p>Within these layout sections you can sort the content items by means of drag & drop.<br /> You can make an unlimited number of pages and structure them hierarchical. Your page structure can be displayed in a multi level menu in your website.</p>', 'zikulacontentmodule')
        ]);
        $contentType = $this->displayHelper->initContentType($item);
        $item->setSearchText($contentType->getSearchableText());
        $item->setAdditionalSearchText($this->translator->__('Content pages may contain many different types of content items.', 'zikulacontentmodule'));
        $contentInfo[] = [$item, 'mid', ['x' => 0, 'y' => (0 * $itemHeightEditing), 'width' => 8, 'minWidth' => 2]];

        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'QuoteType');
        $item->setContentData([
            'text' => $this->translator->__('Zikula allows you to build simple one-page websites to individual web applications.', 'zikulacontentmodule'),
            'source' => $this->translator->__('https://ziku.la/en/', 'zikulacontentmodule'),
            'description' => $this->translator->__('Zikula homepage', 'zikulacontentmodule')
        ]);
        $contentType = $this->displayHelper->initContentType($item);
        $item->setSearchText($contentType->getSearchableText());
        $item->setAdditionalSearchText($this->translator->__('Zikula and Content provide powerful tools for creating websites and web applications.', 'zikulacontentmodule'));
        $contentInfo[] = [$item, 'mid', ['x' => 8, 'y' => (0 * $itemHeightEditing), 'width' => 4, 'minWidth' => 2]];

        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'HtmlType');
        $item->setContentData([
            'text' => $this->translator->__('<p><strong>This is a second HTML text content item in the left column.</strong><br />Content is an extendable module. You can create your own content plugins and other Zikula modules can also offer additional content items. For example a calendar module may provide a Content plugin for a list of the latest events.</p>', 'zikulacontentmodule')
        ]);
        $contentType = $this->displayHelper->initContentType($item);
        $item->setSearchText($contentType->getSearchableText());
        $item->setAdditionalSearchText($this->translator->__('Content can be extended by other modules which can contribute additional content types.', 'zikulacontentmodule'));
        $contentInfo[] = [$item, 'mid', ['x' => 0, 'y' => (1 * $itemHeightEditing), 'width' => 8, 'minWidth' => 2]];

        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'ComputerCodeType');
        $item->setContentData([
            'text' => $this->translator->__('$this->doAction($var); // just some code', 'zikulacontentmodule'),
            'codeFilter' => 'native'
        ]);
        $contentType = $this->displayHelper->initContentType($item);
        $item->setSearchText($contentType->getSearchableText());
        $contentInfo[] = [$item, 'mid', ['x' => 8, 'y' => (1 * $itemHeightEditing), 'width' => 4, 'minWidth' => 2]];

        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'HtmlType');
        $item->setContentData([
            'text' => $this->translator->__('<p>So you see that you can place all kinds of content on the page in your own style and liking. This makes Content a really powerful module.</p><p>It also features additional functionality, like translating content and tracking changes between different versions.</p>', 'zikulacontentmodule')
        ]);
        $contentType = $this->displayHelper->initContentType($item);
        $item->setSearchText($contentType->getSearchableText());
        $item->setAdditionalSearchText($this->translator->__('Content also offers functionality for versioning and managing translations.', 'zikulacontentmodule'));
        $contentInfo[] = [$item, 'mid', ['x' => 8, 'y' => (2 * $itemHeightEditing), 'width' => 4, 'minWidth' => 2]];

        $item = new ContentItemEntity();
        $item->setOwningType($contentTypeNamespace . 'HtmlType');
        $adminPageListUrl = $this->router->generate('zikulacontentmodule_page_adminview');
        $adminSettingsUrl = $this->router->generate('zikulacontentmodule_config_config');
        $item->setContentData([
            'text' => $this->translator->__f('This <strong>footer</strong> finishes this introduction page. And now, please enjoy using Content. The <a href="%adminPageListUrl">Page list</a> interface lets you edit or delete this introduction page. In the <a href="%adminSettingsUrl">administration settings</a> you can further control the Content module.', ['%adminPageListUrl' => $adminPageListUrl, '%adminSettingsUrl' => $adminSettingsUrl], 'zikulacontentmodule')
        ]);
        $contentType = $this->displayHelper->initContentType($item);
        $item->setSearchText($contentType->getSearchableText());
        $contentInfo[] = [$item, 'footer', ['x' => 0, 'y' => (0 * $itemHeightEditing), 'width' => 12, 'minWidth' => 2]];

        // execute the workflow action for each entity
        $flashBag = $this->requestStack->getCurrentRequest()->getSession()->getFlashBag();
        $action = 'submit';
        try {
            $success = true;

            $layoutData = [
                'header' => ['id' => 'section1', 'stylingClasses' => '', 'widgets' => []]
            ];
            foreach ($mainContentInfo as $itemInfo) {
                $item = $itemInfo[0];
                $destinationRow = $itemInfo[1];
                $layoutInfo = $itemInfo[2];

                $success &= $this->workflowHelper->executeAction($item, $action);

                $mainPage->addContentItems($item);
                $layoutInfo['id'] = $item->getId();
                $layoutData[$destinationRow]['widgets'][] = $layoutInfo;
            }
            $layoutData = [$layoutData['header']];
            $mainPage->setLayout($layoutData);
            $success &= $this->workflowHelper->executeAction($mainPage, $action);

            $layoutData = [
                'header' => ['id' => 'section1', 'stylingClasses' => '', 'widgets' => []],
                'mid' => ['id' => 'section2', 'stylingClasses' => '', 'widgets' => []],
                'footer' => ['id' => 'section3', 'stylingClasses' => '', 'widgets' => []]
            ];
            foreach ($contentInfo as $itemInfo) {
                $item = $itemInfo[0];
                $destinationRow = $itemInfo[1];
                $layoutInfo = $itemInfo[2];

                $success &= $this->workflowHelper->executeAction($item, $action);

                $page->addContentItems($item);
                $layoutInfo['id'] = $item->getId();
                $layoutData[$destinationRow]['widgets'][] = $layoutInfo;
            }
            $layoutData = [$layoutData['header'], $layoutData['mid'], $layoutData['footer']];
            $page->setLayout($layoutData);

            $success &= $this->workflowHelper->executeAction($page, $action);
            $flashBag->add('success', $this->translator->__('An example page for introduction with several content items has been created.', 'zikulacontentmodule'));
        } catch (\Exception $exception) {
            $flashBag->add('warning', $this->translator->__('Warning! Could not create the example page for introduction.', 'zikulacontentmodule'));
            $flashBag->add('error', $this->translator->__('Exception during example data creation', 'zikulacontentmodule') . ': ' . $exception->getMessage());
            $this->logger->error('{app}: Could not completely create example data after installation. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]);
        
            return false;
        }
    }

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param ContentDisplayHelper $displayHelper
     */
    public function setContentDisplayHelper(ContentDisplayHelper $displayHelper)
    {
        $this->displayHelper = $displayHelper;
    }
}
