<?php

echo $view->slider('MessageSizeMax', $view::SLIDER_ENUMERATIVE | $view::LABEL_ABOVE)
    ->setAttribute('label', $T('Queue message max size (${0})'));

echo $view->slider('MessageQueueLifetime', $view::SLIDER_ENUMERATIVE | $view::LABEL_ABOVE)
    ->setAttribute('label', $T('Queue message lifetime (${0})'));

$smartHostParams = $view->columns()
    ->insert($view->panel()
        ->insert($view->textInput('SmartHostName'))
        ->insert($view->textInput('SmartHostPort'))
    )
    ->insert($view->panel()
    ->insert($view->textInput('SmartHostUsername'))
    ->insert($view->textInput('SmartHostPassword', $view::TEXTINPUT_PASSWORD))
    )

;

echo $view->fieldsetSwitch('SmartHostStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($smartHostParams)
    ->insert($view->checkbox('SmartHostTlsStatus', 'disabled')->setAttribute('uncheckedValue', 'enabled'))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP | $view::BUTTON_CANCEL);