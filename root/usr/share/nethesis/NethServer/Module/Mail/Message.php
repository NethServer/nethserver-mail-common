<?php
namespace NethServer\Module\Mail;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;
use Nethgui\Controller\Table\Modify as Table;

/**
 * Change queue Message properties
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Message extends \Nethgui\Controller\AbstractController
{

    public function initialize()
    {
        $this->declareParameter('MessageSizeMax', $this->createValidator(Validate::POSITIVE_INTEGER)->lessThan(1024), array('configuration', 'postfix', 'MessageSizeMax'));
        $this->declareParameter('MessageQueueLifetime', $this->createValidator(Validate::POSITIVE_INTEGER)->lessThan(31), array('configuration', 'postfix', 'MessageQueueLifetime'));
        $this->declareParameter('SmartHostStatus', Validate::SERVICESTATUS, array('configuration', 'postfix', 'SmartHostStatus'));
        $this->declareParameter('SmartHostName', Validate::HOSTNAME, array('configuration', 'postfix', 'SmartHostName'));
        $this->declareParameter('SmartHostPort', Validate::PORTNUMBER, array('configuration', 'postfix', 'SmartHostPort'));
        $this->declareParameter('SmartHostUsername', Validate::ANYTHING, array('configuration', 'postfix', 'SmartHostUsername'));
        $this->declareParameter('SmartHostPassword', Validate::ANYTHING, array('configuration', 'postfix', 'SmartHostPassword'));
        $this->declareParameter('SmartHostTlsStatus', Validate::SERVICESTATUS, array('configuration', 'postfix', 'SmartHostTlsStatus'));
        parent::initialize();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {       
        $view['MessageSizeMaxDatasource'] = \Nethgui\Renderer\AbstractRenderer::hashToDatasource(array(
                '10' => '10 MB',
                '20' => '20 MB',
                '50' => '50 MB',
                '100' => '100 MB',
                '200' => '200 MB',
                '500' => '500 MB',
                '1024' => '1 GB',
            ));

        $view['MessageQueueLifetimeDatasource'] = \Nethgui\Renderer\AbstractRenderer::hashToDatasource(array(                
                '1' => $view->translate('${0} day', array(1)),
                '2' => $view->translate('${0} days', array(2)),
                '4' => $view->translate('${0} days', array(4)),
                '7' => $view->translate('${0} days', array(7)),
                '15' => $view->translate('${0} days', array(15)),
                '30' => $view->translate('${0} days', array(30)),
            ));
        
        parent::prepareView($view);
    }

}