<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

use Zikula\ContentModule\AbstractContentType;

/**
 * Block content type.
 */
class BlockType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'cubes';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Block');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Display Zikula blocks.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'blockId' => 0
        ];
    }

/** TODO
    function display()
    {
        $id = $this->blockid;
        $blockinfo = BlockUtil::getBlockInfo($id);
        $modinfo = ModUtil::getInfo($blockinfo['mid']);
        $text = BlockUtil::show($modinfo['name'], $blockinfo['bkey'], $blockinfo);
        $this->view->assign('content', $text);
        return $this->view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        $id = $this->blockid;
        $blockinfo = BlockUtil::getBlockInfo($id);

        $output = $blockinfo['title'] . ' (ID=' . $this->blockid . ')';
        return $output;
    }
    function startEditing()
    {
        $blocksInfo = BlockUtil::getBlocksInfo();
        $blockoptions = array();
        // add first empty choice
        $blockoptions[] = array('text' => __('- Make a choice -'), 'value' => '0');
        foreach ($blocksInfo as $block) {
                $blockoptions[] = array('text' => $block['bid'] . ' - ' . $block['title'] . ' (' . ($block['active']?__('Active'):__('InActive')) . ')', 'value' => $block['bid']);
        }
        $this->view->assign('blockoptions', $blockoptions);
    }
*/
    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return ''; // TODO
    }
}
