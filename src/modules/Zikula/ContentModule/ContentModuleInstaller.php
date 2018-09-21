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

namespace Zikula\ContentModule;

use Zikula\ContentModule\Base\AbstractContentModuleInstaller;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\PageCategoryEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;

/**
 * Installer implementation class.
 */
class ContentModuleInstaller extends AbstractContentModuleInstaller
{
    /**
     * @inheritDoc
     */
    public function install()
    {
        $result = parent::install();
        if (!$result) {
            return $result;
        }

        $this->setVar('pageStyles', "product|Product page\nlegal|Legal page");
        $this->setVar('sectionStyles', "header|Header\nreferences|References\nfooter|Footer");
        $this->setVar('contentStyles', "grey-box|Grey box\nred-box|Red box\nyellow-box|Yellow box\ngreen-box|Green box\norange-announcement-box|Orange announcement box\ngreen-important-box|Green important box");

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function upgrade($oldVersion)
    {
        if (version_compare($oldVersion, '5.0.0', '<')) {
            ini_set('memory_limit', '2048M');
            ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

            // delete all old data
            $variableApi = $this->container->get('zikula_extensions_module.api.variable');
            $variableApi->delAll('content');
            $variableApi->delAll('Content');

            // reinstall
            $this->install();

            // determine category registry identifier
            $registryRepository = $this->entityManager->getRepository('ZikulaCategoriesModule:CategoryRegistryEntity');
            $categoryRegistries = $registryRepository->findBy(['modname' => 'ZikulaContentModule']);
            $categoryRegistry = null;
            foreach ($categoryRegistries as $registry) {
                if ($registry->getEntityname() == 'PageEntity') {
                    $categoryRegistry = $registry;
                    break;
                }
            }

            $conn = $this->getConnection();
            $dbName = $this->getDbName();
            $userRepository = $this->container->get('zikula_users_module.user_repository');

            $pageMap = [];
            $pageLanguageMap = [];
            $categoryMap = [];
            $userMap = [];

            // migrate pages, primary category assignments, page translations
            $stmt = $conn->executeQuery("
                SELECT *
                FROM $dbName.`content_page`
                ORDER BY `page_ppid`, `page_id`
            ");
            while ($row = $stmt->fetch()) {
                $oldPageId = $row['page_id'];

                $page = new PageEntity();
                $page->setWorkflowState('approved');
                $oldParentPageId = $row['page_ppid'];
                if ($oldParentPageId > 0 && isset($pageMap[$oldParentPageId])) {
                    $page->setParent($pageMap[$oldParentPageId]);
                }
                $page->setTitle($row['page_title']);
                $page->setShowTitle(boolval($row['page_showtitle']));
                $page->setMetaDescription($row['page_metadescription']);
                $page->setSkipHookSubscribers(boolval($row['page_nohooks']));
                $page->setViews(intval($row['page_views']));
                $page->setActive(boolval($row['page_active']));
                $activeFrom = $row['page_activefrom'];
                if (null !== $activeFrom && '' != $activeFrom) {
                    $page->setActiveFrom(new \DateTime($activeFrom));
                }
                $activeTo = $row['page_activeto'];
                if (null !== $activeTo && '' != $activeTo) {
                    $page->setActiveTo(new \DateTime($activeTo));
                }
                $page->setInMenu(boolval($row['page_inmenu']));
                if (isset($row['page_optString1'])) {
                    $page->setOptionalString1($row['page_optString1']);
                }
                if (isset($row['page_optString2'])) {
                    $page->setOptionalString2($row['page_optString2']);
                }
                if (isset($row['page_optText'])) {
                    $page->setOptionalText($row['page_optText']);
                }
                $uid = $row['page_cr_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $userRepository->find($uid);
                }
                $page->setCreatedBy($userMap[$uid]);
                $page->setCreatedDate(new \DateTime($row['page_cr_date']));
                $uid = $row['page_lu_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $userRepository->find($uid);
                }
                $page->setUpdatedBy($userMap[$uid]);
                $page->setUpdatedDate(new \DateTime($row['page_lu_date']));

                $page->setLocale($row['page_language']);
                $this->entityManager->persist($page);

                if (null !== $categoryRegistry) {
                    $categoryId = intval($row['page_categoryid']);
                    if ($categoryId > 0) {
                        if (!isset($categoryMap[$categoryId])) {
                            $categoryMap[$categoryId] = $this->entityManager->find('ZikulaCategoriesModule:CategoryEntity', $categoryId);
                        }
                        // check if category still exists
                        if (null !== $categoryMap[$categoryId]) {
                            $page->getCategories()->add(new PageCategoryEntity($categoryRegistry->getId(), $categoryMap[$categoryId], $page));
                        }
                    }
                }

                $this->entityManager->flush($page);

                $pageMap[$oldPageId] = $page;
                $pageLanguageMap[$oldPageId] = $page->getLocale();

                $transStmt = $conn->executeQuery("
                    SELECT `transp_lang`, `transp_title`, `transp_metadescription`
                    FROM $dbName.`content_translatedpage`
                    WHERE `transp_pid` = " . intval($oldPageId) . "
                ");
                while ($transRow = $transStmt->fetch()) {
                    $page->setTitle($transRow['transp_title']);
                    $page->setMetaDescription($transRow['transp_metadescription']);

                    $page->setLocale($transRow['transp_lang']);
                    $this->entityManager->flush($page);
                }
            }

            // migrate content and content translations
            $contentTypeNamespace = 'Zikula\\ContentModule\\ContentType\\';
            $contentDisplayHelper = $this->container->get('zikula_content_module.content_display_helper');
            $stmt = $conn->executeQuery("
                SELECT *
                FROM $dbName.`content_content`
                ORDER BY `con_pageid`, `con_areaindex`, `con_position`
            ");
            while ($row = $stmt->fetch()) {
                $oldContentItemId = $row['con_id'];
                $oldPageId = $row['con_pageid'];
                if (!isset($pageMap[$oldPageId])) {
                    continue;
                }

                $page = $pageMap[$oldPageId];

                $isSupported = true;
                if ($row['con_module'] != 'Content') {
                    $isSupported = false;
                } elseif (in_array($row['con_type'], ['Camtasia', 'FlashMovie', 'Flickr', 'JoinPosition'])) {
                    $isSupported = false;
                }

                $item = new ContentItemEntity();
                $item->setWorkflowState('approved');
                if (!$isSupported) {
                    $item->setOwningType($contentTypeNamespace . 'HtmlType');
                    $item->setContentData([
                        'text' => $this->__f('<p>There has been an element of the <strong>%module%</strong> with type <strong>%type%</strong> which could not be migrated during the Content module upgrade.</p>', ['%module%' => $row['con_module'], '%type%' => $row['con_type']])
                    ]);
                } else {
                    $contentTypeName = $row['con_type'] . 'Type';
                    if ('GoogleMapRouteType' == $contentTypeName) {
                        $contentTypeName = 'GoogleRouteType';
                    } elseif ('ModuleFuncType' == $contentTypeName) {
                        $contentTypeName = 'ControllerType';
                    } elseif ('OpenStreetMapType' == $contentTypeName) {
                        $contentTypeName = 'LeafletMapType';
                    } elseif ('RssType' == $contentTypeName) {
                        $contentTypeName = 'FeedType';
                    }
                    $item->setOwningType($contentTypeNamespace . $contentTypeName);

                    $contentData = @unserialize($row['con_data']);
                    if ($contentData) {
                        if ('AuthorType' == $contentTypeName && isset($contentData['uid'])) {
                            $contentData['author'] = $contentData['uid'];
                            unset($contentData['uid']);
                        } if ('BlockType' == $contentTypeName && isset($contentData['blockid'])) {
                            $contentData['blockId'] = $contentData['blockid'];
                            unset($contentData['blockid']);
                        }
                        $item->setContentData($contentData);
                    }
                }
                $item->setActive(boolval($row['con_active']));
                $scope = intval($row['con_visiblefor']);
                if ($scope < 1) {
                    $item->setScope(-1);
                } elseif ($scope == 2) {
                    $item->setScope(-2);
                }
                if (!empty($row['con_styleclass'])) {
                    $item->setStylingClasses([$row['con_styleclass']]);
                }

                $contentType = $contentDisplayHelper->initContentType($item);
                $item->setSearchText($contentType->getSearchableText());

                $uid = $row['con_cr_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $userRepository->find($uid);
                }
                $item->setCreatedBy($userMap[$uid]);
                $item->setCreatedDate(new \DateTime($row['con_cr_date']));
                $uid = $row['con_lu_uid'];
                if (!isset($userMap[$uid])) {
                    $userMap[$uid] = $userRepository->find($uid);
                }
                $item->setUpdatedBy($userMap[$uid]);
                $item->setUpdatedDate(new \DateTime($row['con_lu_date']));

                $item->setLocale($pageLanguageMap[$oldPageId]);
                $page->addContentItems($item);

                $this->entityManager->persist($item);
                $this->entityManager->flush($item);
                if (!$isSupported) {
                    continue;
                }

                $transStmt = $conn->executeQuery("
                    SELECT `transc_lang`, `transc_data`
                    FROM $dbName.`content_translatedcontent`
                    WHERE `transc_cid` = " . intval($oldContentItemId) . "
                ");
                while ($transRow = $transStmt->fetch()) {
                    $contentData = @unserialize($row['transc_data']);
                    if ($contentData) {
                        $contentData = array_merge($item->getContentData(), $contentData);
                        $item->setContentData($contentData);
                    }

                    $item->setLocale($transRow['transc_lang']);
                    $this->entityManager->flush($item);
                }
            }

            // remove old tables
            $conn->executeQuery("DROP TABLE $dbName.`content_history`");
            $conn->executeQuery("DROP TABLE $dbName.`content_searchable`");
            $conn->executeQuery("DROP TABLE $dbName.`content_translatedcontent`");
            $conn->executeQuery("DROP TABLE $dbName.`content_content`");
            $conn->executeQuery("DROP TABLE $dbName.`content_translatedpage`");
            $conn->executeQuery("DROP TABLE $dbName.`content_pagecategory`");
            $conn->executeQuery("DROP TABLE $dbName.`content_page`");

            $this->addFlash('success', $this->__f('Done! Migrated %amount% pages.', ['%amount%' => count($pageMap)]));

            $oldVersion = '5.0.0';
        }

        switch ($oldVersion) {
            case '5.0.0':
                // future upgrades
        }

        // update successful
        return true;
    }

    /**
     * Returns connection to the database.
     *
     * @return Connection the current connection
     */
    private function getConnection()
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $connection = $entityManager->getConnection();

        return $connection;
    }
    /**
     * Returns the name of the default system database.
     *
     * @return string the database name
     */
    private function getDbName()
    {
        return $this->container->getParameter('database_name');
    }
}
