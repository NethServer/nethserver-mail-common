<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = $T('Update domain `${0}`');
    $keyStyles = $view::STATE_READONLY;
} else {
    $headerText = $T('Create a new domain');
    $keyStyles = 0;
}

echo $view->header('username')->setAttribute('template', $headerText);

echo $view->textInput('domain', $keyStyles);
echo $view->textInput('Description');


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP | $view::BUTTON_CANCEL);