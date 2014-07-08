<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */


namespace wizacha\BBQ\Job;
use \Eventio\BBQ\Job\Job;


class SqsJob extends Job {

    public function __construct(
        $payload,
        \Guzzle\Service\Resource\Model $sqsResource
    )
    {
        parent::__construct($payload);
        $this->_sqsResource = $sqsResource;
    }

    protected $_sqsResource = null;

    public function getSqsResource()
    {
        return $this->_sqsResource;
    }
} 