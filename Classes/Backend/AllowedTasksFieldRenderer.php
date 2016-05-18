<?php
namespace NamelessCoder\Rpc\Backend;

use NamelessCoder\Rpc\Manager\TaskManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AllowedTasksFieldRenderer
 */
class AllowedTasksFieldRenderer {

    /**
     * @param array $parameters
     * @return string
     */
    public function renderField(array $parameters) {
        $selectedValues = GeneralUtility::trimExplode(',', $parameters['row']['allowed_tasks']);
        $tasks = TaskManager::getInstance()->getTaskIds();
        $fieldName = $parameters['itemFormElName'];

        $html = sprintf('<div id="%s" class="form-group">', $parameters['itemFormElID']);
        foreach ($tasks as $taskId) {
            $task = TaskManager::getInstance()->getTaskById($taskId);
            if (!$task->getTaskConfiguration()->isAvailable()) {
                continue;
            }
            $html .= '<label class="help-block">';
            $html .= sprintf(
                '<input type="checkbox" name="%s[]" value="%s" %s /> ',
                $fieldName,
                $taskId,
                in_array($taskId, $selectedValues) ? 'checked="checked"' : ''
            );
            $html .= $task->getTaskConfiguration()->getLabel();
            $html .= '<br />';
            $html .= '<em class="text-muted">' . $task->getTaskConfiguration()->getDescription() . '</em>';
            $html .= '</label>';
        }
        $html .= '</div>';

        return $html;
    }

}
