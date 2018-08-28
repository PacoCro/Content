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

use Zikula\ContentModule\Helper\Base\AbstractLoggableHelper;
use Zikula\ContentModule\Entity\PageEntity;

/**
 * Helper implementation class for loggable behaviour.
 */
class LoggableHelper extends AbstractLoggableHelper
{
    /**
     * @inheritDoc
     */
    protected function translateActionDescriptionInternal($text = '', array $parameters = [])
    {
        $actionTranslated = parent::translateActionDescriptionInternal($text, $parameters);
        switch ($text) {
            case '_HISTORY_PAGE_CONTENT_CREATED':
                $actionTranslated = $this->__('Content created');
                break;
            case '_HISTORY_PAGE_CONTENT_UPDATED':
                $actionTranslated = $this->__('Content updated');
                break;
            case '_HISTORY_PAGE_CONTENT_CLONED':
                $actionTranslated = $this->__('Content cloned');
                break;
            case '_HISTORY_PAGE_CONTENT_DELETED':
                $actionTranslated = $this->__('Content deleted');
                break;
            case '_HISTORY_PAGE_LAYOUT_CHANGED':
                $actionTranslated = $this->__('Layout changed (e.g. content moved or resized)');
                break;
        }

        return $actionTranslated;
    }

    /**
     * Stores data about a page's content items and their translations into the contentData
     * field of the owning page in order to add this information into the revisioning.
     *
     * @param PageEntity $page
     */
    public function updateContentData(PageEntity $page)
    {
        $contentData = [];
        $entityManager = $this->entityFactory->getObjectManager();
        $translationRepository = $entityManager->getRepository('Zikula\ContentModule\Entity\ContentItemTranslationEntity');
        $supportedLanguages = $this->translatableHelper->getSupportedLanguages('contentItem');
        $fields = $this->translatableHelper->getTranslatableFields('contentItem');

        foreach ($page->getContentItems() as $item) {
            $itemData = [
                'id' => $item->getId(),
                'workflowState' => $item->getWorkflowState(),
                'owningType' => $item->getOwningType(),
                'contentData' => $item->getContentData(),
                'active' => $item->getActive(),
                'activeFrom' => $item->getActiveFrom(),
                'activeTo' => $item->getActiveTo(),
                'scope' => $item->getScope(),
                'stylingClasses' => $item->getStylingClasses(),
                'searchText' => $item->getSearchText(),
                'additionalSearchText' => $item->getAdditionalSearchText(),
                'translations' => []
            ];

            // collect translations
            $entityTranslations = $translationRepository->findTranslations($item);
            foreach ($supportedLanguages as $language) {
                $translationData = [];
                foreach ($fields as $fieldName) {
                    $translationData[$fieldName] = isset($entityTranslations[$language][$fieldName]) ? $entityTranslations[$language][$fieldName] : '';
                }
                // add data to collected translations
                $itemData['translations'][$language] = $translationData;
            }

            $contentData[] = $itemData;
        }

        $page->setContentData($contentData);
    }

    /**
     * @inheritDoc
     */
    public function revert($entity, $requestedVersion = 1, $detach = false)
    {
        $entity = parent::revert($entity, $requestedVersion, $detach);
        if (!($entity instanceof PageEntity)) {
            return $entity;
        }

        $entityManager = $this->entityFactory->getObjectManager();
        $currentLanguage = $this->translatableHelper->getCurrentLanguage();

        // revert content items
        foreach ($entity->getContentItems() as $item) {
            $entity->removeContentItems($item);
            if (true === $detach) {
                $entityManager->detach($item);
            } else {
                $entityManager->remove($item);
            }
        }

        $contentData = $entity->getContentData();
        foreach ($contentData as $itemData) {
            $translations = $itemData['translations'];
            unset($itemData['translations']);

            $newItem = $this->entityFactory->createContentItem();
            $newItem->merge($itemData);

            $entity->addContentItems($newItem);
            if (true === $detach) {
                $entityManager->detach($newItem);

                if (isset($translations[$currentLanguage])) {
                    foreach ($translations[$currentLanguage] as $fieldName => $fieldData) {
                        if ('contentData' == $fieldName) {
                            $fieldData = @unserialize($fieldData);
                        }
                        $setter = 'set' . ucfirst($fieldName);
                        $newItem->$setter($fieldData);
                    }
                    $newItem->setLocale($currentLanguage);
                }
            } else {
                $entityManager->flush($newItem);
                foreach ($translations as $language => $translationData) {
                    foreach ($translationData as $fieldName => $fieldData) {
                        if ('contentData' == $fieldName) {
                            $fieldData = @unserialize($fieldData);
                        }
                        $setter = 'set' . ucfirst($fieldName);
                        $newItem->$setter($fieldData);
                    }
                    $newItem->setLocale($language);
                    $entityManager->flush($newItem);
                }
            }
        }

        if (true !== $detach) {
            $entityManager->flush($entity);
        }

        return $entity;
    }
}
