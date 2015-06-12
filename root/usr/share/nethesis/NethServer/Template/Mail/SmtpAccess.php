<?php

/* @var $view \Nethgui\Renderer\Xhtml */

echo $view->checkBox('AccessPolicyTrustedNetworks', 'yes')->setAttribute('uncheckedValue', 'no');
echo $view->textArea('AccessBypassList', $view::LABEL_ABOVE)->setAttribute('dimensions', '5x30');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
