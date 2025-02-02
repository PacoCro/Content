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

namespace Zikula\ContentModule\HookSubscriber\Base;

use Zikula\Bundle\HookBundle\Category\FilterHooksCategory;
use Zikula\Bundle\HookBundle\HookSubscriberInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Base class for filter hooks subscriber.
 */
abstract class AbstractContentItemFilterHooksSubscriber implements HookSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getOwner(): string
    {
        return 'ZikulaContentModule';
    }
    
    public function getCategory(): string
    {
        return FilterHooksCategory::NAME;
    }
    
    public function getTitle(): string
    {
        return $this->translator->__('Content item filter hooks subscriber');
    }
    
    public function getAreaName(): string
    {
        return 'subscriber.zikulacontentmodule.filter_hooks.contentitems';
    }

    public function getEvents(): array
    {
        return [
            FilterHooksCategory::TYPE_FILTER => 'zikulacontentmodule.filter_hooks.contentitems.filter'
        ];
    }
}
