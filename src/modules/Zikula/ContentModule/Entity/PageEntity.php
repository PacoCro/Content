<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Entity;

use Zikula\ContentModule\Entity\Base\AbstractPageEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for page entities.
 * @Gedmo\Loggable(logEntryClass="\Zikula\ContentModule\Entity\PageLogEntryEntity")
 * @Gedmo\TranslationEntity(class="Zikula\ContentModule\Entity\PageTranslationEntity")
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Zikula\ContentModule\Entity\Repository\PageRepository")
 * @ORM\Table(name="zikula_content_page",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 * @UniqueEntity(fields="slug", ignoreNull="false")
 */
class PageEntity extends BaseEntity
{
    // feel free to add your own methods here
}
