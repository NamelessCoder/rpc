<?php
namespace NamelessCoder\Rpc\Implementation\Field;

use NamelessCoder\Rpc\ClientComponent\Field\PopupField;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;

/**
 * Class InstalledExtensionsField
 */
class InstalledExtensionsField extends PopupField {

    /**
     * Override the type of field.
     *
     * @return string
     */
    public function getType() {
        return 'PopupField';
    }

    /**
     * @return array
     */
    public function getOptions() {
        /** @var ListUtility $listUtility */
        $listUtility = GeneralUtility::makeInstance(ObjectManager::class)->get(ListUtility::class);
        $allExtensionKeys = array_keys($listUtility->getAvailableExtensions());
        return $this->detectInstalledExtensionKeys($allExtensionKeys);
    }

    /**
     * @param array $allExtensionKeys
     * @return array
     */
    protected function detectInstalledExtensionKeys(array $allExtensionKeys) {
        $installed = [];
        foreach ($allExtensionKeys as $extensionKey) {
            if (ExtensionManagementUtility::isLoaded($extensionKey)) {
                $installed[] = $extensionKey;
            }
        }
        return $installed;
    }

}

