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

namespace Zikula\ContentModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Core\Event\GenericEvent;

/**
 * Event handler base class for dispatching modules.
 */
abstract class AbstractModuleDispatchListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'module_dispatch.service_links' => ['serviceLinks', 5]
        ];
    }
    
    /**
     * Listener for the `module_dispatch.service_links` event.
     *
     * Occurs when building admin menu items.
     * Adds sublinks to a Services menu that is appended to all modules if populated.
     * Triggered by module_dispatch.postexecute in bootstrap.
     *
     * Inject router and translator services and format data like this:
     *     `$event->data[] = [
     *         'url' => $router->generate('zikulacontentmodule_user_index'),
     *         'text' => $translator->__('Link text')
     *     ];`
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function serviceLinks(GenericEvent $event): void
    {
    }
}
