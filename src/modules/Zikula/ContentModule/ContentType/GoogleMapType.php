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
 * Google map content type.
 */
class GoogleMapType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_EXTERNAL;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'map-marker';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Google map');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Display a Google map position.');
    }

    /**
     * @inheritDoc
     */
    public function getAdminInfo()
    {
        return $this->__('You need to specify a Google Maps API key in the configuration form in order to activate this plugin.');
    }

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        // Only active when the API key is available
        // TODO
        return true;
        return false;//'' != ModUtil::getVar('ZikulaContentModule', 'googleMapsApiKey', '');
    }

    /**
     * @inheritDoc
     */
    public function isTranslatable()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'latitude' => '55.8756960390043',
            'longitude' => '12.36185073852539',
            'zoom' => 5,
            'height' => 400,
            'text' => '',
            'infoText' => '',
            'streetViewControl' => false,
            'directionsLink' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

/** TODO
    function display()
    {
        $apiKey = ModUtil::getVar('Content', 'googlemapApiKey');
        if (empty($apiKey)) {
            return '';
        }

        PageUtil::addVar('javascript', 'https://maps.googleapis.com/maps/api/js?key='.$apiKey.'&language=' . ZLanguage::getLanguageCode() . '&sensor=false');

        return $this->view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        return $this->__f('Map at longitude: %1$s, latitude: %2$s, description: %3$s', array(substr($this->longitude,0,6).'...', substr($this->latitude,0,6).'...', DataUtil::formatForDisplay($this->text)));
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
