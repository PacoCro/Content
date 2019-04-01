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

use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\ComputerCodeType as FormType;

/**
 * Computer code content type.
 */
class ComputerCodeType extends AbstractContentType
{
    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'code';
    }

    public function getTitle(): string
    {
        return $this->__('Computer code');
    }

    public function getDescription(): string
    {
        return $this->__('A text editor for computer code. Line numbers are added to the text and it is displayed in a monospaced font.');
    }

    public function getDefaultData(): array
    {
        return [
            'text' => '',
            'codeFilter' => 'native'
        ];
    }

    public function getSearchableText(): string
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

    public function displayView(): string
    {
        $this->data['formattedText'] = '';

        $unformattedText = $this->data['text'];
        if ('bbcode' === $this->data['codeFilter'] && $this->kernel->isBundle('ZikulaBBCodeModule')) {
            $this->data['formattedText'] = $this->transformCode($unformattedText);
            /* @todo update as soon as the BBCode module has been migrated
            $code = '[code]' . $unformattedText . '[/code]';
            PageUtil::addVar('stylesheet', 'modules/BBCode/style/style.css');
            $this->data['formattedText'] = ModUtil::apiFunc('BBCode', 'user', 'transform', array('message' => $code));
            */
        } elseif ('lumicula' === $this->data['codeFilter'] && $this->kernel->isBundle('PhaidonLuMicuLaModule')) {
            $this->data['formattedText'] = $this->transformCode($unformattedText);
            /* @todo update as soon as the LuMicuLa module has been migrated
            */
        } else {
            $this->data['formattedText'] = $this->transformCode($unformattedText);
        }

        return parent::displayView();
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    /**
     * Processes the code.
     */
    protected function transformCode(string $code): string
    {
        $lines = explode("\n", $code);
        $html = '<ol class="codelisting">' . "\n";

        for ($i = 1, $amountOfLines = count($lines); $i <= $amountOfLines; ++$i) {
            $line = empty($lines[$i - 1]) ? ' ' : htmlspecialchars($lines[$i - 1]);
            $line = '<div><pre>' . $line . '</pre></div>';
            $html .= "<li>$line</li>\n";
        }

        $html .= '</ol>' . "\n";

        return $html;
    }

    /**
     * @required
     */
    public function setKernel(ZikulaHttpKernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }
}
