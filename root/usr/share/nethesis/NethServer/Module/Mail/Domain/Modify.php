<?php
namespace NethServer\Module\Mail\Domain;

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
 * Modify domain
 *
 * Generic class to create/update/delete Domain records
 * 
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Modify extends \Nethgui\Controller\Table\Modify
{

    public function initialize()
    {
        $parameterSchema = array(
            array('domain', Validate::HOSTNAME_FQDN, Table::KEY),
            array('Description', Validate::ANYTHING, Table::FIELD),
            array('TransportType', Validate::ANYTHING, Table::FIELD),
        );

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('TransportType', 'Reject');

        parent::initialize();
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\Mail\Domain\Modify',
            'update' => 'NethServer\Template\Mail\Domain\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);
    }

    public function onParametersSaved($changedParameters)
    {
        if ($this->getIdentifier() === 'update') {
            $event = 'modify';
        } else {
            $event = $this->getIdentifier();
        }
        $this->getPlatform()->signalEvent(sprintf('domain-%s@post-process', $event), array($this->parameters['domain']));
    }

}