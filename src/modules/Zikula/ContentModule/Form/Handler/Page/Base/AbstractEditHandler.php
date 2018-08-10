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

namespace Zikula\ContentModule\Form\Handler\Page\Base;

use Zikula\ContentModule\Form\Handler\Common\EditHandler;
use Zikula\ContentModule\Form\Type\PageType;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use RuntimeException;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * This handler class handles the page events of editing forms.
 * It aims on the page object type.
 */
abstract class AbstractEditHandler extends EditHandler
{
    /**
     * @inheritDoc
     */
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
    
        if ($this->templateParameters['mode'] == 'create') {
            if (!$this->modelHelper->canBeCreated($this->objectType)) {
                $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add('error', $this->__('Sorry, but you can not create the page yet as other items are required which must be created before!'));
                $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => $this->objectType];
                $this->logger->notice('{app}: User {user} tried to create a new {entity}, but failed as it other items are required which must be created before.', $logArgs);
    
                return new RedirectResponse($this->getRedirectUrl(['commandName' => '']), 302);
            }
        }
        
        if ($this->templateParameters['mode'] == 'edit') {
            $this->requestStack->getCurrentRequest()->getSession()->set('ZikulaContentModuleEntityVersion', $this->entityRef->getCurrentVersion());
        }
    
        $entityData = $this->entityRef->toArray();
    
        // assign data to template as array (for additions like standard fields)
        $this->templateParameters[$this->objectTypeLower] = $entityData;
    
        return $result;
    }
    
    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        return $this->formFactory->create(PageType::class, $this->entityRef, $this->getFormOptions());
    }
    
    /**
     * @inheritDoc
     */
    protected function getFormOptions()
    {
        $options = [
            'mode' => $this->templateParameters['mode'],
            'actions' => $this->templateParameters['actions'],
            'has_moderate_permission' => $this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADMIN),
            'filter_by_ownership' => !$this->permissionHelper->hasEntityPermission($this->entityRef, ACCESS_ADD),
            'inline_usage' => $this->templateParameters['inlineUsage']
        ];
    
        $options['translations'] = [];
        foreach ($this->templateParameters['supportedLanguages'] as $language) {
            $options['translations'][$language] = isset($this->templateParameters[$this->objectTypeLower . $language]) ? $this->templateParameters[$this->objectTypeLower . $language] : [];
        }
    
        return $options;
    }


    /**
     * @inheritDoc
     */
    protected function initEntityForEditing()
    {
        $entity = parent::initEntityForEditing();
        if (null === $entity) {
            return $entity;
        }
    
        $this->originalSlug = $entity->getSlug();
        $slugParts = explode('/', $entity->getSlug());
        $entity->setSlug(end($slugParts));
    
        return $entity;
    }

    /**
     * @inheritDoc
     */
    protected function getRedirectCodes()
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
     *
     * @param array $args List of arguments
     *
     * @return string The default redirect url
     */
    protected function getDefaultReturnUrl(array $args = [])
    {
        $objectIsPersisted = $args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel');
    
        if (null !== $this->returnTo) {
            $refererParts = explode('/', $this->returnTo);
            $isDisplayOrEditPage = $refererParts[count($refererParts)-2] == 'page';
            if ($isDisplayOrEditPage) {
                // update slug for proper redirect to display/edit page
                $refererParts[count($refererParts)-1] = $this->entityRef->getSlug();
                $this->returnTo = implode('/', $refererParts);
            }
            if (!$isDisplayOrEditPage || $objectIsPersisted) {
                // return to referer
                return $this->returnTo;
            }
        }
    
        $routeArea = array_key_exists('routeArea', $this->templateParameters) ? $this->templateParameters['routeArea'] : '';
        $routePrefix = 'zikulacontentmodule_' . $this->objectTypeLower . '_' . $routeArea;
    
        // redirect to the list of pages
        $url = $this->router->generate($routePrefix . 'view', ['tpl' => 'tree']);
    
        if ($objectIsPersisted) {
            // redirect to the detail page of treated page
            $url = $this->router->generate($routePrefix . 'display', ['slug' => $this->originalSlug]);
        }
    
        return $url;
    }

    /**
     * @inheritDoc
     */
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
        if ($this->templateParameters['mode'] == 'create' && $this->form->has('submitrepeat') && $this->form->get('submitrepeat')->isClicked()) {
            $args['commandName'] = 'submit';
            $this->repeatCreateAction = true;
        }
    
        return new RedirectResponse($this->getRedirectUrl($args), 302);
    }
    
    /**
     * @inheritDoc
     */
    protected function getDefaultMessage(array $args = [], $success = false)
    {
        if (false === $success) {
            return parent::getDefaultMessage($args, $success);
        }
    
        $message = '';
        switch ($args['commandName']) {
            case 'submit':
                if ($this->templateParameters['mode'] == 'create') {
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
    public function applyAction(array $args = [])
    {
        // get treated entity reference from persisted member var
        $entity = $this->entityRef;
    
        $action = $args['commandName'];
        
        $applyLock = $this->templateParameters['mode'] != 'create' && $action != 'delete';
        $expectedVersion = $this->requestStack->getCurrentRequest()->getSession()->get('ZikulaContentModuleEntityVersion', 1);
    
        $success = false;
        $flashBag = $this->requestStack->getCurrentRequest()->getSession()->getFlashBag();
        try {
            if ($applyLock) {
                // assert version
                $this->entityFactory->getObjectManager()->lock($entity, LockMode::OPTIMISTIC, $expectedVersion);
            }
            
            // execute the workflow action
            $success = $this->workflowHelper->executeAction($entity, $action);
        } catch (OptimisticLockException $exception) {
            $flashBag->add('error', $this->__('Sorry, but someone else has already changed this record. Please apply the changes again!'));
            $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => 'page', 'id' => $entity->getKey()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed as someone else has already changed it.', $logArgs);
        } catch (\Exception $exception) {
            $flashBag->add('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => $action]) . ' ' . $exception->getMessage());
            $logArgs = ['app' => 'ZikulaContentModule', 'user' => $this->currentUserApi->get('uname'), 'entity' => 'page', 'id' => $entity->getKey(), 'errorMessage' => $exception->getMessage()];
            $this->logger->error('{app}: User {user} tried to edit the {entity} with id {id}, but failed. Error details: {errorMessage}.', $logArgs);
        }
    
        $this->addDefaultMessage($args, $success);
    
        if ($success && $this->templateParameters['mode'] == 'create') {
            // store new identifier
            $this->idValue = $entity->getKey();
        }
    
        return $success;
    }

    /**
     * Get URL to redirect to.
     *
     * @param array $args List of arguments
     *
     * @return string The redirect url
     */
    protected function getRedirectUrl(array $args = [])
    {
        if ($this->repeatCreateAction) {
            return $this->repeatReturnUrl;
        }
    
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if ($session->has('zikulacontentmodule' . $this->objectTypeCapital . 'Referer')) {
            $session->remove('zikulacontentmodule' . $this->objectTypeCapital . 'Referer');
        }
    
        // normal usage, compute return url from given redirect code
        if (!in_array($this->returnTo, $this->getRedirectCodes())) {
            // invalid return code, so return the default url
            return $this->getDefaultReturnUrl($args);
        }
    
        $routeArea = substr($this->returnTo, 0, 5) == 'admin' ? 'admin' : '';
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
                if ($args['commandName'] != 'delete' && !($this->templateParameters['mode'] == 'create' && $args['commandName'] == 'cancel')) {
                    return $this->router->generate($routePrefix . 'display', $this->entityRef->createUrlArgs());
                }
    
                return $this->getDefaultReturnUrl($args);
            default:
                return $this->getDefaultReturnUrl($args);
        }
    }
}
