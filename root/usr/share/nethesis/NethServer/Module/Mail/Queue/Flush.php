<?php
namespace NethServer\Module\Mail\Queue;

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

/**
 * Flush mail queue
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Flush extends \Nethgui\Controller\Table\AbstractAction
{
    /**
     *
     * @var integer
     */
    private $messageCount = 0;

    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            $this->getPlatform()->exec('/usr/bin/sudo /usr/sbin/postqueue -f');
        }
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ( ! $this->getRequest()->isMutation()) {
            $this->messageCount = count($this->getAdapter());
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['messageCount'] = $this->messageCount;
        if ($this->getRequest()->isMutation()) {
            $view->getCommandList()->sendQuery($view->getModuleUrl('../read') . '?deferred=1', 3000, TRUE);
        }
    }

}
