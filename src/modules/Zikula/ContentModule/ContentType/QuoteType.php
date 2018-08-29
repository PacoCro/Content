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

namespace Zikula\ContentModule\ContentType;

use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\QuoteType as FormType;

/**
 * Quote content type.
 */
class QuoteType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'quote-right';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Quote');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('A highlighted quote with source.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'text' => $this->__('Add quote text here...'),
            'source' => 'https://',
            'description' => $this->__('Name of the source')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTranslatableDataFields()
    {
        return ['text', 'source', 'description'];
    }

    /**
     * @inheritDoc
     */
    public function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return FormType::class;
    }
}
