<?php
namespace NamelessCoder\Rpc\ViewHelpers;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PayloadDisplayViewHelper
 */
class PayloadDisplayViewHelper extends AbstractViewHelper {

    /**
     * @return void
     */
    public function initializeArguments() {
        $this->registerArgument('payload', 'mixed', 'Payload to be displayed in an HTML friendly format');
    }

    /**
     * @return string
     */
    public function render() {
        return static::renderStatic($this->arguments, $this->buildRenderChildrenClosure(), $this->renderingContext);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
        return static::renderPayload($arguments['payload'] ? : $renderChildrenClosure());
    }


    protected static function renderPayload($payload) {
        if (is_array($payload)) {
            return static::renderPayloadAsArray($payload);
        } elseif (is_scalar($payload)) {
            return $payload;
        }
        return 'Cannot display payload, type: ' . gettype($payload);
    }

    /**
     * @param array $payload
     * @return string
     */
    protected static function renderPayloadAsArray(array $payload) {
        $html = '<table class="table table-bordered">';
        $html .= sprintf(
            '<thead><tr><th>%s</th><th>%s</th></tr>',
            LocalizationUtility::translate('response.payload.attribute', 'rpc'),
            LocalizationUtility::translate('response.payload.value', 'rpc')
        );
        foreach ($payload as $name => $value) {
            $html .= '<tr>';
            $html .= '<td class="col-xs-3 text-monospace">' . $name . '</td>';
            $html .= '<td class="col-xs-9">';
            $html .= static::renderPayload($value);
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

}
