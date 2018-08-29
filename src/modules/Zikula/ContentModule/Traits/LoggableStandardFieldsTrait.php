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

namespace Zikula\ContentModule\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\UsersModule\Entity\UserEntity;

/**
 * Loggable standard fields trait implementation class.
 */
trait LoggableStandardFieldsTrait
{
    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Zikula\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(referencedColumnName="uid")
     * @Gedmo\Versioned
     * @var UserEntity
     */
    protected $createdBy;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Versioned
     * @Assert\DateTime()
     * @var \DateTimeInterface $createdDate
     */
    protected $createdDate;
    
    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Zikula\UsersModule\Entity\UserEntity")
     * @ORM\JoinColumn(referencedColumnName="uid")
     * @Gedmo\Versioned
     * @var UserEntity
     */
    protected $updatedBy;
    
    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Gedmo\Versioned
     * @Assert\DateTime()
     * @var \DateTimeInterface $updatedDate
     */
    protected $updatedDate;
    
    /**
     * Returns the created by.
     *
     * @return UserEntity
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    
    /**
     * Sets the created by.
     *
     * @param UserEntity $createdBy
     *
     * @return void
     */
    public function setCreatedBy($createdBy)
    {
        if ($this->createdBy != $createdBy) {
            $this->createdBy = $createdBy;
        }
    }
    
    /**
     * Returns the created date.
     *
     * @return \DateTimeInterface
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
    
    /**
     * Sets the created date.
     *
     * @param \DateTimeInterface $createdDate
     *
     * @return void
     */
    public function setCreatedDate($createdDate)
    {
        if ($this->createdDate != $createdDate) {
            $this->createdDate = $createdDate;
        }
    }
    
    /**
     * Returns the updated by.
     *
     * @return UserEntity
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    
    /**
     * Sets the updated by.
     *
     * @param UserEntity $updatedBy
     *
     * @return void
     */
    public function setUpdatedBy($updatedBy)
    {
        if ($this->updatedBy != $updatedBy) {
            $this->updatedBy = $updatedBy;
        }
    }
    
    /**
     * Returns the updated date.
     *
     * @return \DateTimeInterface
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
    
    /**
     * Sets the updated date.
     *
     * @param \DateTimeInterface $updatedDate
     *
     * @return void
     */
    public function setUpdatedDate($updatedDate)
    {
        if ($this->updatedDate != $updatedDate) {
            $this->updatedDate = $updatedDate;
        }
    }
    
}
