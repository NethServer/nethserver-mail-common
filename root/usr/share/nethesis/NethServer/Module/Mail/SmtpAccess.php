<?php

namespace NethServer\Module\Mail;

/*
 * Copyright (C) 2015 Nethesis Srl
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of Access
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 */
class SmtpAccess extends \Nethgui\Controller\AbstractController
{

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('AccessBypassList', $this->createValidator(TRUE), array('configuration', 'postfix', 'AccessBypassList'));
        $this->declareParameter('AccessPolicyTrustedNetworks', \Nethgui\System\PlatformInterface::YES_NO, array('configuration', 'postfix', 'AccessPolicies'));
    }

    public static function splitLines($text)
    {
        return array_filter(preg_split("/[,;\s]+/", $text));
    }

    public function readAccessBypassList($dbList)
    {
        return implode("\r\n", explode(',' ,$dbList));
    }

    public function writeAccessBypassList($viewText)
    {
        return array(implode(',', self::splitLines($viewText)));
    }

    public function readAccessPolicyTrustedNetworks($dbList)
    {
        return in_array('trustednetworks', explode(',', $dbList)) ? 'yes' : 'no';
    }

    public function writeAccessPolicyTrustedNetworks($viewText)
    {
        $condition = $viewText === 'yes';
        $values = explode(',', $this->getPlatform()->getDatabase('configuration')->getProp('postfix', 'AccessPolicies'));
        $status = in_array('trustednetworks', $values);
        if ($condition != $status) {
            $values = $status ? array_diff($values, array('trustednetworks')) : array_merge($values, array('trustednetworks'));
        }
        return array(implode(',', array_filter($values)));
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);
        $itemValidator = $this->getPlatform()->createValidator(\Nethgui\System\PlatformInterface::IP);

        foreach (self::splitLines($this->parameters['AccessBypassList']) as $v) {
            if ( ! $itemValidator->evaluate($v)) {
                $report->addValidationErrorMessage($this, 'AccessBypassList', 'IP address list', array($v));
                break;
            }
        }
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('nethserver-mail-common-save &');
    }
}
