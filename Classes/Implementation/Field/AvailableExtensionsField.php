<?php
namespace NamelessCoder\Rpc\Implementation\Field;

use NamelessCoder\Rpc\ClientComponent\Field\PopupField;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;

/**
 * Class AvailableExtensionsField
 */
class AvailableExtensionsField extends PopupField {

    /**
     * @var boolean
     */
    protected $includeInstalled = FALSE;

    /**
     * Override the type of field.
     *
     * @return string
     */
    public function getType() {
        return 'PopupField';
    }

    /**
     * @return boolean
     */
    public function getIncludeInstalled() {
        return $this->includeInstalled;
    }

    /**
     * @param boolean $includeInstalled
     */
    public function setIncludeInstalled($includeInstalled) {
        $this->includeInstalled = $includeInstalled;
    }

    /**
     * @return array
     */
    public function getOptions() {
        /** @var ListUtility $listUtility */
        $listUtility = GeneralUtility::makeInstance(ObjectManager::class)->get(ListUtility::class);
        $listOfExtensions = $listUtility->getAvailableExtensions();
        $keys = array_keys($listOfExtensions);
        if ($this->includeInstalled) {
            return $keys;
        }
        $notInstalled = array();
        foreach ($keys as $key) {
            if (!ExtensionManagementUtility::isLoaded($key)) {
                $notInstalled[] = $key;
            }
        }
        return $notInstalled;
    }

}
