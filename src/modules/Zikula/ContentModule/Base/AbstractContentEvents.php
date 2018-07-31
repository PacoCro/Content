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

namespace Zikula\ContentModule\Base;

/**
 * Events definition base class.
 */
abstract class AbstractContentEvents
{
    /**
     * The zikulacontentmodule.page_post_load event is thrown when pages
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterPageEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const PAGE_POST_LOAD = 'zikulacontentmodule.page_post_load';
    
    /**
     * The zikulacontentmodule.page_pre_persist event is thrown before a new page
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterPageEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const PAGE_PRE_PERSIST = 'zikulacontentmodule.page_pre_persist';
    
    /**
     * The zikulacontentmodule.page_post_persist event is thrown after a new page
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterPageEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const PAGE_POST_PERSIST = 'zikulacontentmodule.page_post_persist';
    
    /**
     * The zikulacontentmodule.page_pre_remove event is thrown before an existing page
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterPageEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const PAGE_PRE_REMOVE = 'zikulacontentmodule.page_pre_remove';
    
    /**
     * The zikulacontentmodule.page_post_remove event is thrown after an existing page
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterPageEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const PAGE_POST_REMOVE = 'zikulacontentmodule.page_post_remove';
    
    /**
     * The zikulacontentmodule.page_pre_update event is thrown before an existing page
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterPageEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const PAGE_PRE_UPDATE = 'zikulacontentmodule.page_pre_update';
    
    /**
     * The zikulacontentmodule.page_post_update event is thrown after an existing new page
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterPageEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const PAGE_POST_UPDATE = 'zikulacontentmodule.page_post_update';
    
    /**
     * The zikulacontentmodule.contentitem_post_load event is thrown when content items
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterContentItemEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const CONTENTITEM_POST_LOAD = 'zikulacontentmodule.contentitem_post_load';
    
    /**
     * The zikulacontentmodule.contentitem_pre_persist event is thrown before a new content item
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterContentItemEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const CONTENTITEM_PRE_PERSIST = 'zikulacontentmodule.contentitem_pre_persist';
    
    /**
     * The zikulacontentmodule.contentitem_post_persist event is thrown after a new content item
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterContentItemEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const CONTENTITEM_POST_PERSIST = 'zikulacontentmodule.contentitem_post_persist';
    
    /**
     * The zikulacontentmodule.contentitem_pre_remove event is thrown before an existing content item
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterContentItemEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const CONTENTITEM_PRE_REMOVE = 'zikulacontentmodule.contentitem_pre_remove';
    
    /**
     * The zikulacontentmodule.contentitem_post_remove event is thrown after an existing content item
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterContentItemEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const CONTENTITEM_POST_REMOVE = 'zikulacontentmodule.contentitem_post_remove';
    
    /**
     * The zikulacontentmodule.contentitem_pre_update event is thrown before an existing content item
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterContentItemEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const CONTENTITEM_PRE_UPDATE = 'zikulacontentmodule.contentitem_pre_update';
    
    /**
     * The zikulacontentmodule.contentitem_post_update event is thrown after an existing new content item
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterContentItemEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const CONTENTITEM_POST_UPDATE = 'zikulacontentmodule.contentitem_post_update';
    
    /**
     * The zikulacontentmodule.searchable_post_load event is thrown when searchables
     * are loaded from the database.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterSearchableEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postLoad()
     * @var string
     */
    const SEARCHABLE_POST_LOAD = 'zikulacontentmodule.searchable_post_load';
    
    /**
     * The zikulacontentmodule.searchable_pre_persist event is thrown before a new searchable
     * is created in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterSearchableEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::prePersist()
     * @var string
     */
    const SEARCHABLE_PRE_PERSIST = 'zikulacontentmodule.searchable_pre_persist';
    
    /**
     * The zikulacontentmodule.searchable_post_persist event is thrown after a new searchable
     * has been created in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterSearchableEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postPersist()
     * @var string
     */
    const SEARCHABLE_POST_PERSIST = 'zikulacontentmodule.searchable_post_persist';
    
    /**
     * The zikulacontentmodule.searchable_pre_remove event is thrown before an existing searchable
     * is removed from the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterSearchableEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::preRemove()
     * @var string
     */
    const SEARCHABLE_PRE_REMOVE = 'zikulacontentmodule.searchable_pre_remove';
    
    /**
     * The zikulacontentmodule.searchable_post_remove event is thrown after an existing searchable
     * has been removed from the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterSearchableEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postRemove()
     * @var string
     */
    const SEARCHABLE_POST_REMOVE = 'zikulacontentmodule.searchable_post_remove';
    
    /**
     * The zikulacontentmodule.searchable_pre_update event is thrown before an existing searchable
     * is updated in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterSearchableEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::preUpdate()
     * @var string
     */
    const SEARCHABLE_PRE_UPDATE = 'zikulacontentmodule.searchable_pre_update';
    
    /**
     * The zikulacontentmodule.searchable_post_update event is thrown after an existing new searchable
     * has been updated in the system.
     *
     * The event listener receives an
     * Zikula\ContentModule\Event\FilterSearchableEvent instance.
     *
     * @see Zikula\ContentModule\Listener\EntityLifecycleListener::postUpdate()
     * @var string
     */
    const SEARCHABLE_POST_UPDATE = 'zikulacontentmodule.searchable_post_update';
    
}
