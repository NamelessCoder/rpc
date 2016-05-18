<?php
namespace NamelessCoder\Rpc\Backend;

use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * Class DataHandlerHook
 */
class DataHandlerHook {

    /**
     * @param string $status
     * @param string $table
     * @param mixed $id
     * @param array $fieldArray
     * @param DataHandler $parent
     * @return void
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $parent) {
        if (isset($fieldArray['allowed_tasks'])) {
            $fieldArray['allowed_tasks'] = implode(',', $fieldArray['allowed_tasks']);
        }
    }

}
