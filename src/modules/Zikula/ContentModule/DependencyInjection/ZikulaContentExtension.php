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

namespace Zikula\ContentModule\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\DependencyInjection\Base\AbstractZikulaContentExtension;

/**
 * Implementation class for service definition loader using the DependencyInjection extension.
 */
class ZikulaContentExtension extends AbstractZikulaContentExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        parent::load($configs, $container);

        $container->registerForAutoconfiguration(ContentTypeInterface::class)
            ->addTag('zikula.content_type')
            ->setPublic(true)
            ->setShared(false)
        ;
    }
}
