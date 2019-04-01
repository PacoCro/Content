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

namespace Zikula\ContentModule\Base;

use Exception;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula\CategoriesModule\Api\CategoryPermissionApi;
use Zikula\CategoriesModule\Entity\CategoryRegistryEntity;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRegistryRepositoryInterface;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRepositoryInterface;
use Zikula\Common\Translator\Translator;
use Zikula\UsersModule\Api\CurrentUserApi;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\PageLogEntryEntity;
use Zikula\ContentModule\Entity\PageTranslationEntity;
use Zikula\ContentModule\Entity\PageCategoryEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\ContentItemTranslationEntity;

/**
 * Installer base class.
 */
abstract class AbstractContentModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var string[]
     */
    protected $entities = [
        PageEntity::class,
        PageLogEntryEntity::class,
        PageTranslationEntity::class,
        PageCategoryEntity::class,
        ContentItemEntity::class,
        ContentItemTranslationEntity::class,
    ];

    public function install(): bool
    {
        $logger = $this->container->get('logger');
        $userName = $this->container->get(CurrentUserApi::class)->get('uname');
    
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->entities);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('stateOfNewPages', '1');
        $this->setVar('countPageViews', false);
        $this->setVar('googleMapsApiKey', '');
        $this->setVar('yandexTranslateApiKey', '');
        $this->setVar('enableRawPlugin', false);
        $this->setVar('inheritPermissions', false);
        $this->setVar('enableAutomaticPageLinks', true);
        $this->setVar('pageStyles', 'dummy|Dummy');
        $this->setVar('sectionStyles', 'dummy|Dummy');
        $this->setVar('contentStyles', 'dummy|Dummy');
        $this->setVar('enableOptionalString1', false);
        $this->setVar('enableOptionalString2', false);
        $this->setVar('enableOptionalText', false);
        $this->setVar('ignoreBundleNameInRoutes', true);
        $this->setVar('ignoreEntityNameInRoutes', true);
        $this->setVar('ignoreFirstTreeLevelInRoutes', true);
        $this->setVar('permalinkSuffix', 'none');
        $this->setVar('pageEntriesPerPage', 10);
        $this->setVar('linkOwnPagesOnAccountPage', true);
        $this->setVar('pagePrivateMode', false);
        $this->setVar('showOnlyOwnEntries', false);
        $this->setVar('allowModerationSpecificCreatorForPage', false);
        $this->setVar('allowModerationSpecificCreationDateForPage', false);
        $this->setVar('enabledFinderTypes', 'page');
        $this->setVar('revisionHandlingForPage', 'unlimited');
        $this->setVar('maximumAmountOfPageRevisions', '25');
        $this->setVar('periodForPageRevisions', 'P1Y0M0DT0H0M0S');
        $this->setVar('showPageHistory', true);
    
        // add default entry for category registry (property named Main)
        $categoryHelper = new \Zikula\ContentModule\Helper\CategoryHelper(
            $this->container->get(Translator::class),
            $this->container->get('request_stack'),
            $logger,
            $this->container->get(CurrentUserApi::class),
            $this->container->get(CategoryRegistryRepositoryInterface::class),
            $this->container->get(CategoryPermissionApi::class)
        );
        $categoryGlobal = $this->container->get(CategoryRepositoryInterface::class)->findOneBy(['name' => 'Global']);
        if ($categoryGlobal) {
            $categoryRegistryIdsPerEntity = [];
    
            $registry = new CategoryRegistryEntity();
            $registry->setModname('ZikulaContentModule');
            $registry->setEntityname('PageEntity');
            $registry->setProperty($categoryHelper->getPrimaryProperty('Page'));
            $registry->setCategory($categoryGlobal);
    
            try {
                $this->entityManager->persist($registry);
                $this->entityManager->flush();
            } catch (Exception $exception) {
                $this->addFlash('warning', $this->__f('Error! Could not create a category registry for the %entity% entity. If you want to use categorisation, register at least one registry in the Categories administration.', ['%entity%' => 'page']));
                $logger->error('{app}: User {user} could not create a category registry for {entities} during installation. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'user' => $userName, 'entities' => 'pages', 'errorMessage' => $exception->getMessage()]);
            }
            $categoryRegistryIdsPerEntity['page'] = $registry->getId();
        }
    
        // initialisation successful
        return true;
    }
    
    public function upgrade(string $oldVersion): bool
    {
    /*
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->entities);
                } catch (Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]);
    
                    return false;
                }
        }
    */
    
        // update successful
        return true;
    }
    
    public function uninstall(): bool
    {
        $logger = $this->container->get('logger');
    
        try {
            $this->schemaTool->drop($this->entities);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // remove all module vars
        $this->delVars();
    
        // remove category registry entries
        $registries = $this->container->get(CategoryRegistryRepositoryInterface::class)->findBy(['modname' => 'ZikulaContentModule']);
        foreach ($registries as $registry) {
            $this->entityManager->remove($registry);
        }
        $this->entityManager->flush();
    
        // uninstallation successful
        return true;
    }
}
