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
 * Table-like interface for postfix mailq output
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class MailQueueAdapter implements \ArrayAccess, \Nethgui\Adapter\AdapterInterface, \Countable, \IteratorAggregate, \Nethgui\System\PlatformConsumerInterface
{
    /**
     *
     * @var \Nethgui\System\PlatformInterface
     */
    private $platform;

    /**
     *
     * @var \ArrayObject
     */
    private $data;

    public function __construct(\Nethgui\System\PlatformInterface $platform)
    {
        $this->platform = $platform;
    }

    public function save()
    {
        throw new \LogicException(sprintf("%s: read-only adapter, %s() method is not allowed", __CLASS__, __METHOD__), 1354208730);
    }

    public function set($value)
    {
        throw new \LogicException(sprintf("%s: read-only adapter, %s() method is not allowed", __CLASS__, __METHOD__), 1354208727);
    }

    public function delete()
    {
        throw new \LogicException(sprintf("%s: read-only adapter, %s() method is not allowed", __CLASS__, __METHOD__), 1354208722);
    }

    public function get()
    {
        if ( ! isset($this->data)) {
            $this->lazyInitialization();
        }

        return $this->data;
    }

    public function isModified()
    {
        return FALSE;
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException(sprintf("%s: read-only adapter, %s() method is not allowed", __CLASS__, __METHOD__), 1354208725);
    }

    public function offsetExists($offset)
    {
        if ( ! isset($this->data)) {
            $this->lazyInitialization();
        }

        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        if ( ! isset($this->data)) {
            $this->lazyInitialization();
        }

        return $this->data[$offset];
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException(sprintf("%s: read-only adapter, %s() method is not allowed", __CLASS__, __METHOD__), 1354208721);
    }

    public function count()
    {
        if ( ! isset($this->data)) {
            $this->lazyInitialization();
        }

        return $this->data->count();
    }

    public function getIterator()
    {
        if ( ! isset($this->data)) {
            $this->lazyInitialization();
        }
        return $this->data->getIterator();
    }

    private function lazyInitialization()
    {
        $data = json_decode($this->getPlatform()->exec('/usr/bin/sudo /usr/bin/mailq | /usr/libexec/nethserver/mailq2json')->getOutput(), TRUE);

        $this->data = new \ArrayObject();

        foreach ($data as $message) {

            $recipients = $this->getAllRecipients($message);

            $row = array(
                'Id' => $message['id'],
                'Sender' => $message['sender'],
                'Status' => $message['status'],
                'Size' => $this->formatSize($message['size']),
                'Timestamp' => $message['time'],
                'Recipients' => implode(', ', array_slice($recipients, 0, 3)),
                'RecipientsCount' => count($recipients),
                'Problems' => array_keys($message['reasons'])
            );

            $this->data[$message['id']] = $row;
        }
    }

    private function getAllRecipients($message)
    {
        $recipients = $message['recipients'];
        foreach ($message['reasons'] as $r) {
            $recipients = array_merge($recipients, $r);
        }
        return $recipients;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function hasPlatform()
    {
        return isset($this->platform);
    }

    public function setPlatform(\Nethgui\System\PlatformInterface $platform)
    {
        $this->platform = $platform;
        return $this;
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