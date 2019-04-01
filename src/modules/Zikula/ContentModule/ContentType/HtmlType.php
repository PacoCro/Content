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

namespace Zikula\ContentModule\ContentType;

use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\HtmlType as FormType;

/**
 * HTML content type.
 */
class HtmlType extends AbstractContentType
{
    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'font';
    }

    public function getTitle(): string
    {
        return $this->__('HTML text');
    }

    public function getDescription(): string
    {
        return $this->__('HTML editor for adding markup text to your page.');
    }

    public function getDefaultData(): array
    {
        return [
            'text' => $this->__('Add text here...')
        ];
    }

    public function getTranslatableDataFields(): array
    {
        return ['text'];
    }

    public function getSearchableText(): string
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    public function getAssets(string $context): array
    {
        $assets = parent::getAssets($context);
        if (in_array($context, [ContentTypeInterface::CONTEXT_EDIT, ContentTypeInterface::CONTEXT_TRANSLATION], true)) {
            $assets['js'][] = $this->assetHelper->resolve('@ZikulaContentModule:js/ZikulaContentModule.ContentType.Html.js');
        }

        return $assets;
    }

    public function getJsEntrypoint(string $context): ?string
    {
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            return 'contentInitHtmlEdit';
        }
        if (ContentTypeInterface::CONTEXT_TRANSLATION === $context) {
            return 'contentInitHtmlTranslation';
        }

        return null;
    }
}
