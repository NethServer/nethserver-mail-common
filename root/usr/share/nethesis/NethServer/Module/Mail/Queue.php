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

/**
 * Manage the mail queue
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Queue extends \Nethgui\Controller\TableController
{

    public function initialize()
    {
        $columns = array(
            'Id',
            'Sender',
            'Size',
            'Timestamp',
            'Recipients',
            'Actions',
        );

        $this
            ->setTableAdapter(new \Nethgui\Adapter\LazyLoaderAdapter(array($this, 'readMailQueue')))
            ->setColumns($columns)
            ->addTableAction(new Queue\Flush())
            ->addTableAction(new Queue\Refresh())
            ->addRowAction(new Queue\Delete())
        ;

        parent::initialize();
    }

    public function prepareViewForColumnId(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $rowMetadata['rowCssClass'] .= ' valign-top padicon';
        if ($values['Status'] === 'HOLD') {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' locked');
        } elseif ($values['Status'] === 'ACTIVE') {
            $rowMetadata['rowCssClass'] = trim($rowMetadata['rowCssClass'] . ' sync');
        }
        return $values['Id'];
    }

    public function prepareViewForColumnRecipients(\Nethgui\Controller\Table\Read $action, \Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $recipients = $values['Recipients'];
        if (count($recipients) <= 3) {
            return implode(', ', $recipients);
        }
        return implode(', ', array_merge(array_slice($recipients, 0, 2), array($view->translate('AndXMore', array(count($recipients) - 2)))));
    }

    public function readMailQueue()
    {
        $messages = json_decode($this->getPlatform()->exec('/usr/bin/sudo /usr/sbin/postqueue -p | /usr/libexec/nethserver/mailq2json')->getOutput(), TRUE);

        $data = new \ArrayObject();

        foreach ($messages as $message) {

            $recipients = $this->getAllRecipients($message);

            $row = array(
                'Id' => $message['id'],
                'Sender' => $message['sender'],
                'Status' => $message['status'],
                'Size' => $this->formatSize($message['size']),
                'Timestamp' => $message['time'],
                'Recipients' => $recipients,
                'RecipientsCount' => (string) count($recipients),
                'Problems' => array_keys($message['reasons'])
            );

            $data[$message['id']] = $row;
        }

        return $data;
    }

    private function getAllRecipients($message)
    {
        $recipients = $message['recipients'];
        foreach ($message['reasons'] as $r) {
            $recipients = array_merge($recipients, $r);
        }
        return $recipients;
    }

    private function formatSize($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size > 1024; $i ++ ) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }

}
