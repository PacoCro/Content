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

namespace Zikula\ContentModule\Block\Base;

use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Zikula\BlocksModule\AbstractBlockHandler;
use Zikula\ContentModule\Block\Form\Type\ItemBlockType;
use Zikula\ContentModule\Helper\ControllerHelper;

/**
 * Generic item detail block base class.
 */
abstract class AbstractItemBlock extends AbstractBlockHandler
{
    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;
    
    /**
     * @var FragmentHandler
     */
    protected $fragmentHandler;
    
    public function getType(): string
    {
        return $this->__('Content detail', 'zikulacontentmodule');
    }
    
    public function display(array $properties = []): string
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('ZikulaContentModule:ItemBlock:', $properties['title'] . '::', ACCESS_OVERVIEW)) {
            return '';
        }
    
        // set default values for all params which are not properly set
        $defaults = $this->getDefaults();
        $properties = array_merge($defaults, $properties);
    
        if (null === $properties['id'] || empty($properties['id'])) {
            return '';
        }
    
        $contextArgs = ['name' => 'detail'];
        if (!isset($properties['objectType']) || !in_array($properties['objectType'], $this->controllerHelper->getObjectTypes('block', $contextArgs), true)) {
            $properties['objectType'] = $this->controllerHelper->getDefaultObjectType('block', $contextArgs);
        }
    
        $controllerReference = new ControllerReference('ZikulaContentModule:External:display', $this->getDisplayArguments($properties), ['template' => $properties['customTemplate']]);
    
        return $this->fragmentHandler->render($controllerReference);
    }
    
    /**
     * Returns common arguments for displaying the selected object using the external controller.
     */
    protected function getDisplayArguments(array $properties = []): array
    {
        return [
            'objectType' => $properties['objectType'],
            'id' => $properties['id'],
            'source' => 'block',
            'displayMode' => 'embed'
        ];
    }
    
    public function getFormClassName(): string
    {
        return ItemBlockType::class;
    }
    
    public function getFormOptions(): array
    {
        $objectType = 'page';
    
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request && $request->attributes->has('blockEntity')) {
            $blockEntity = $request->attributes->get('blockEntity');
            if (is_object($blockEntity) && method_exists($blockEntity, 'getProperties')) {
                $blockProperties = $blockEntity->getProperties();
                if (isset($blockProperties['objectType'])) {
                    $objectType = $blockProperties['objectType'];
                } else {
                    // set default options for new block creation
                    $blockEntity->setProperties($this->getDefaults());
                }
            }
        }
    
        return [
            'object_type' => $objectType
        ];
    }
    
    public function getFormTemplate(): string
    {
        return '@ZikulaContentModule/Block/item_modify.html.twig';
    }
    
    /**
     * Returns default settings for this block.
     */
    protected function getDefaults(): array
    {
        return [
            'objectType' => 'page',
            'id' => null,
            'template' => 'item_display.html.twig',
            'customTemplate' => null
        ];
    }
    
    /**
     * @required
     */
    public function setControllerHelper(ControllerHelper $controllerHelper): void
    {
        $this->controllerHelper = $controllerHelper;
    }
    
    /**
     * @required
     */
    public function setFragmentHandler(FragmentHandler $fragmentHandler): void
    {
        $this->fragmentHandler = $fragmentHandler;
    }
}
