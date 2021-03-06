<?php
/**
 * Copyright 2010-2013 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Aws\Sns\MessageValidator;

use Aws\Common\Exception\InvalidArgumentException;
use Guzzle\Common\Collection;

abstract class AbstractMessage implements MessageInterface
{
    /**
     * @var array A list of valid message types
     */
    protected static $validMessageTypes = array('Notification', 'SubscriptionConfirmation');

    /**
     * @var Collection The message data
     */
    protected $data;

    /**
     * Creates a Message object from an array of raw message data
     *
     * @param array $data The message data
     *
     * @return MessageInterface
     * @throws InvalidArgumentException If a valid type is not provided in the array
     */
    public static function fromArray(array $data)
    {
        if (!isset($data['Type']) || !in_array($data['Type'], self::$validMessageTypes)) {
            throw new InvalidArgumentException('The "Type" key must be provided with a valid value in order to '
                . 'instantiate the Message object.');
        }

        // Resolve the message class name
        /** @var $messageClass AbstractMessage */
        $messageClass = __NAMESPACE__ . "\\{$data['Type']}Message";

        // Create a collection from the message data and make sure required keys are present
        $data = Collection::fromConfig($data, array(), $messageClass::getRequiredKeys());

        return new $messageClass($data);
    }

    /**
     * @param Collection $data A Collection of message data with all required keys
     */
    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->data['Message'];
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageId()
    {
        return $this->data['MessageId'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp()
    {
        return $this->data['Timestamp'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTopicArn()
    {
        return $this->data['TopicArn'];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->data['Type'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSignature()
    {
        return $this->data['Signature'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSigningCertUrl()
    {
        return $this->data['SigningCertURL'];
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getStringToSign();

    /**
     * Returns a list of required keys in the message data. This list in AbstractMessage includes keys that are common
     * among all message types and that are required for message verification. Specific message type classes should
     * overwrite this method to add any additional required keys.
     *
     * @return array
     */
    protected static function getRequiredKeys()
    {
        return array(
            'Message',
            'MessageId',
            'Timestamp',
            'TopicArn',
            'Type',
            'Signature',
            'SigningCertURL',
        );
    }
}
