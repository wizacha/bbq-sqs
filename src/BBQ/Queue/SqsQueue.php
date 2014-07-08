<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */


namespace Wizacha\BBQ\Queue;
use \Eventio\BBQ\Queue\AbstractQueue;
use \Eventio\BBQ\Job\JobInterface;
use \Eventio\BBQ\Job\Payload\JobPayloadInterface;
use \Aws\Sqs\SqsClient;
use \Wizacha\BBQ\Job\SqsJob;


class SqsQueue extends AbstractQueue {

    protected $_client;
    protected $_queueUrl;

    public function __construct(
        $id,
        SqsClient $client,
        $queueUrl,
        array $config = [])
    {
        $this->_client = $client;
        $this->_queueUrl = $queueUrl;

        parent::__construct($id, $config);
    }

    protected function init()
    {

    }

    /**
     * Retrieve one Job
     *
     * @param int|null $timeout Waiting time before closing request if no Job present in stack
     * @return null|SqsJob
     */
    public function fetchJob($timeout = NULL)
    {
        $result = $this->_client->receiveMessage([
            'QueueUrl' => $this->_queueUrl,
            'WaitTimeSeconds' => $timeout?:0, //integer expected, can't be NULL
        ]);

        if (!$result->hasKey('Messages')) {
            return null;
        }

        $job = new SqsJob(unserialize($result->getPath('Messages/*/Body')[0]), $result);
        $job->setQueue($this);

        $this->lockJob($job);

        return $job;
    }

    /**
     * Delete done Job
     *
     * @param JobInterface $job
     * @return bool
     */
    public function finalizeJob(JobInterface $job)
    {
        $result = $this->_client->deleteMessage([
            'QueueUrl' => $this->_queueUrl,
            'ReceiptHandle' => $job->getSqsResource()->getPath('Messages/*/ReceiptHandle')[0],
        ]);

        if($result->count() == 0)
        {
            $this->deleteLockedJob($job);
            return true;
        }

        return false;
    }

    /**
     * Create new Job
     *
     * @param JobPayloadInterface $jobPayload
     * @return bool
     */
    public function pushJob(JobPayloadInterface $jobPayload)
    {
        $result =  $this->_client->sendMessage([
            'QueueUrl'     => $this->_queueUrl,
            'MessageBody'  => serialize($jobPayload),
        ]);

        return !empty($result->get('MessageId'));
    }

    /**
     * Abandon Job and make him visible for other consumers
     *
     * @param JobInterface $job
     * @return bool
     */
    public function releaseJob(JobInterface $job)
    {
        $result = $this->_client->changeMessageVisibility([
            'QueueUrl' => $this->_queueUrl,
            'ReceiptHandle' => $job->getSqsResource()->getPath('Messages/*/ReceiptHandle')[0],
            'VisibilityTimeout' => 0,
        ]);

        if($result->count() == 0)
        {
            $this->deleteLockedJob($job);
            return true;
        }

        return false;
    }
} 