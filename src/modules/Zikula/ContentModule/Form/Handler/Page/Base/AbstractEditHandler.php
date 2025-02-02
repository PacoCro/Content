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

namespace Zikula\ContentModule\Form\Handler\Page\Base;

use Zikula\ContentModule\Form\Handler\Common\EditHandler;
use Zikula\ContentModule\Form\Type\PageType;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\ContentModule\Entity\PageEntity;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the page object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    public function processForm(array $templateParameters = [])
    {
        $this->objectType = 'page';
        $this->objectTypeCapital = 'Page';
        $this->objectTypeLower = 'page';
        
        $this->hasPageLockSupport = true;
        $this->hasTranslatableFields = true;
    
        $result = parent::processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
    
        if ('create' === $this->templateParameters['mode'] && !$this->modelHelper->canBeCreated($this->objectType)) {
            $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', $this->__('Sorry, but you can not create the page yet as other items are required which must be created before!'));
            $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $this->objectType];
            $this->logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', $logArgs);
    
            return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
        }
        
        if ('edit' === $this->templateParameters['mode']) {
            $this->requestStack->getCurrentRequest()->getSession()->set('ZikulaContentModuleEntityVersion', $this->entityRef->getCurrentVersion());
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (for additions like standard fields)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
        $this->templateParameters['supportsHookSubscribers'] = $this->entityRef->supportsHookSubscribers();
    
        return $result;
    }
    
    protected function createForm(): ?FormInterface
    {
        return $this->formFactory->create(PageType::class, $this->entityRef, $this->getFormOptions());
    }
    
    protected function getFormOptions(): array
    {
        $options = [
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'has_moderate_permission' => $this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADMIN),
            'allow_moderation_specific_creator' => (bool)$this->variableApi->get('ZikulaContentModule', 'allowModerationSpecificCreatorFor' . $this->objectTypeCapital),
            'allow_moderation_specific_creation_date' => (bool)$this->variableApi->get('ZikulaContentModule', 'allowModerationSpecificCreationDateFor' . $this->objectTypeCapital),
            'filter_by_ownership' => !$this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADD),
            'inline_usage' => $this->templateParameters['inlineUsage']
        ];
    
        $options['translations'] = [];
        foreach ($this->templateParameters['supportedLanguages'] as $language) {
            $translationKey = $this->objectTypeLower . $language;
            $options['translations'][$language] = $this->templateParameters[$translationKey] ?? [];
        }
    
        return $options;
    }

    protected function initEntityForEditing(): ?EntityAccess
    {
        $entity = parent::initEntityForEditing();
        if (null === $entity) {
            return $entity;
        }
    
        // only allow editing for the owner or people with higher permissions
        $currentUserId = $this->currentUserApi->isLoggedIn() ? $this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        $isOwner = null !== $entity && null !== $entity->getCreatedBy() && $currentUserId === $entity->getCreatedBy()->getUid();
        if (!$isOwner && !$this->permissionHelper->hasEntityPermission($entity, ACCESS_ADD)) {
            throw new AccessDeniedException();
        }
    
        return $entity;
    }

    protected function getRedirectCodes(): array
    {
        $codes = parent::getRedirectCodes();
    
        // user index page of page area
        $codes[] = 'userIndex';
        // admin index page of page area
        $codes[] = 'adminIndex';
        // user list of pages
        $codes[] = 'userView';
        // admin list of pages
        $codes[] = 'adminView';
        // user list of own pages
        $codes[] = 'userOwnView';
        // admin list of own pages
        $codes[] = 'adminOwnView';
        // user detail page of treated page
        $codes[] = 'userDisplay';
        // admin detail page of treated page
        $codes[] = 'adminDisplay';
    
    
        return $codes;
    }

    /**
     * Get the default redirect url. Required if no returnTo parameter has been supplied.
     * This method is called in handleCommand so we know which command has been performed.
     */
    protected function getDefaultReturnUrl(array $args = []): string
    {
        $objectIsPersisted = 'delete' !== $args['commandName'] && !('create' === $this->templateParameters['mode'] && 'cancel' === $args['commandName']);
        if (null !== $this->returnTo && $objectIsPersisted) {
            // return to referer
            return $this->returnTo;
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
        $routePrefix = 'zikulacontentmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the list of pages
        $url = $this->router->generate($routePrefix . 'view');
    
        if ($objectIsPersisted) {
            // redirect to the detail page of treated page
            $url = $this->router->generate($routePrefix . 'display', $this->entityRef->createUrlArgs());
        }
    
        return $url;
    }

    public function handleCommand(array $args = [])
    {
        $result = parent::handleCommand($args);
        if (false === $result) {
            return $result;
        }
    
        // build $args for BC (e.g. used by redirect handling)
        foreach ($this->templateParameters['actions'] as $action) {
            if ($this->form->get($action['id'])->isClicked()) {
                $args['commandName'] = $action['id'];
            }
        }
        if ('create' === $this->templateParameters['mode'] && $this->form->has('submitrepeat') && $this->form->get('submitrepeat')->isClicked()) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    protected function getDefaultMessage(array $args = [], bool $success = false): string
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        switch ($args['commandName']) {
            case 'defer':
            case 'submit':
                if ('create' === $this->templateParameters['mode']) {
                    $message = $this->__('Done! Page created.');
                } else {
                    $message = $this->__('Done! Page updated.');
                }
                break;
            case 'delete':
                $message = $this->__('Done! Page deleted.');
                break;
            default:
                $message = $this->__('Done! Page updated.');
                break;
        }
    
        return $message;
    }

    /**
     * @inheritDoc
     * @throws RuntimeException Thrown if concurrent editing is recognised or another error occurs
     */
    public function applyAction(array $args = []): bool
    {
        // get treated entity reference from persisted member var
        /** @var PageEntity $entity */
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
        if ('delete' === $action) {
            $entity->set_actionDescriptionForLogEntry('_HISTORY_PAGE_DELETED');
        } else if ('create' === $this->templateParameters['mode']) {
            $entity->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CREATED');
        } else {
            $templateId = $this->requestStack->getCurrentRequest()->query->getInt('astemplate');
            if ($templateId > 0) {
                $entityT = $this->entityFactory->getRepository($this->objectType)->selectById($templateId, false, true);
                if (null !== $entityT) {
                    $entity->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CLONED|%page=' . $entityT->getKey());
                }
            }
            if (!$entity->get_actionDescriptionForLogEntry()) {
                $entity->set_actionDescriptionForLogEntry('_HISTORY_PAGE_UPDATED');
            }
        }
        
        $applyLock = 'create' !== $this->templateParameters['mode'] && 'delete' !== $action;
        $expectedVersion = $this->requestStack->getCurrentRequest()->getSession()->get('ZikulaContentModuleEntityVersion', 1);
    
        $success = false;
        $flashBag = $this->requestStack->getCurrentRequest()->getSession()->getFlashBag();
        try {
            if ($applyLock) {
                // assert version
                $this->entityFactory->getEntityManager()->lock($entity, LockMode::OPTIMISTIC, $expectedVersion);
            }
            
            // execute the workflow action
            $success = $this->workflowHelper->executeAction($entity, $action);
        } catch (OptimisticLockException $exception) {
            $flashBag->add('error', $this->__('Sorry, but someone else has already changed this record. Please apply the changes again!'));
            $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => 'page', 'id' => $entity->getKey()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed as someone else has already changed it.', $logArgs);
        } catch (Exception $exception) {
            $flashBag->add('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . ' ' . $exception->getMessage());
            $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => 'page', 'id' => $entity->getKey(), 'errorMessage' => $exception->getMessage()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', $logArgs);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && 'create' === $this->templateParameters['mode']) {
            // store new identifier
            $this->idValue = $entity->getKey();
        }
    
        return $success;
    }

    /**
     * Get URL to redirect to.
     */
    protected function getRedirectUrl(array $args = []): string
    {
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if ($session->has('zikulacontentmodule' . $this->objectTypeCapital . 'Referer')) {
            $this->returnTo = $session->get('zikulacontentmodule' . $this->objectTypeCapital . 'Referer');
            $session->remove('zikulacontentmodule' . $this->objectTypeCapital . 'Referer');
        }
    
        if ('create' !== $this->templateParameters['mode']) {
            // force refresh because slugs may have changed (e.g. by translatable)
            $this->entityFactory->getEntityManager()->clear();
            $this->entityRef = $this->initEntityForEditing();
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes(), true)) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = 0 === strpos($this->returnTo, 'admin') ? 'admin' : '';
        $routePrefix = 'zikulacontentmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // parse given redirect code and return corresponding url
        switch ($this->returnTo) {
            case 'userIndex':
            case 'adminIndex':
                return $this->router->generate($routePrefix . 'index');
            case 'userView':
            case 'adminView':
                return $this->router->generate($routePrefix . 'view');
            case 'userOwnView':
            case 'adminOwnView':
                return $this->router->generate($routePrefix . 'view', [ 'own' => 1 ]);
            case 'userDisplay':
            case 'adminDisplay':
                if ('delete' !== $args['commandName'] && !('create' === $this->templateParameters['mode'] && 'cancel' === $args['commandName'])) {
                    return $this->router->generate($routePrefix . 'display', $this->entityRef->createUrlArgs());
                }
    
                return $this->getDefaultReturnUrl($args);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
