<?php

$view->requireFlag($view::INSET_FORM);

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = $T('Update domain `${0}`');
    $keyStyles = $view::STATE_READONLY;
} else {
    $headerText = $T('Create a new domain');
    $keyStyles = 0;
}

echo $view->header('domain')->setAttribute('template', $headerText);

echo $view->textInput('domain', $keyStyles);
echo $view->textInput('Description');

$transportPanel = $view->fieldset('domain')
    ->setAttribute('template', $T('Messages to domain ${0}'))
    ->insert($view->radioButton('TransportType', 'Reject'))
;

foreach ($view['PlugTransport'] as $pluginView) {
    $value = $pluginView->getModule()->getIdentifier();
    $transportPanel->insert(
        $view->fieldsetSwitch('TransportType', $value, $view::FIELDSETSWITCH_EXPANDABLE)
            ->setAttribute('label', $pluginView->translate('TransportType_' . $value . '_label'))
            ->insert($view->literal($pluginView))
    );
}

echo $transportPanel;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP | $view::BUTTON_CANCEL);