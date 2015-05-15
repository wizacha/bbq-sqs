<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */


namespace Eventio\BBQ\Queue\tests\units;

use \atoum;

abstract class AbstractQueue extends atoum
{

    const QUEUE_NAME = 'queue_name';

    protected $bbq = null;

    public function beforeTestMethod($method)
    {
        $this->bbq = new \Eventio\BBQ();
    }

    public function afterTestMethod($method)
    {
        $this->bbq = null;
    }

    public function testPushFetch()
    {
        $expectedPayload = "Hello World";
        $this->bbq->pushJob(
            self::QUEUE_NAME,
            new \Eventio\BBQ\Job\Payload\StringPayload($expectedPayload)
        );
        $fetchedJob = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this
            ->object($fetchedJob)->isInstanceOf('\Eventio\BBQ\Job\JobInterface')
            ->castToString($fetchedJob->getPayload())->isIdenticalTo($expectedPayload)
        ;
    }

    public function testFinalizeJob()
    {
        $expectedPayload = 'testFinalizeJob';

        $this->bbq->pushJob(
            self::QUEUE_NAME,
            new \Eventio\BBQ\Job\Payload\StringPayload($expectedPayload)
        );

        $job = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this
            ->object($job)->isInstanceOf('\Eventio\BBQ\Job\JobInterface')
            ->castToString($job->getPayload())->isIdenticalTo($expectedPayload)
        ;

        $this->bbq->finalizeJob($job);

        $job = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this
            ->variable($job)->isNull();
    }

    public function testReleaseJob()
    {
        $expectedPayload = 'testReleaseJob';

        $this->bbq->pushJob(
            self::QUEUE_NAME,
            new \Eventio\BBQ\Job\Payload\StringPayload($expectedPayload)
        );

        $job = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this
            ->object($job)->isInstanceOf('\Eventio\BBQ\Job\JobInterface')
            ->castToString($job->getPayload())->isIdenticalTo($expectedPayload);

        $nulljob = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this
            ->variable($nulljob)->isNull();

        $this->bbq->getQueue(self::QUEUE_NAME)->releaseJob($job);

        $job = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this
            ->object($job)->isInstanceOf('\Eventio\BBQ\Job\JobInterface')
            ->castToString($job->getPayload())->isIdenticalTo($expectedPayload);
    }

    public function testEmptyQueue()
    {
        $nullJob = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this->variable($nullJob)->isNull();
        $this->bbq->pushJob(
            self::QUEUE_NAME,
            new \Eventio\BBQ\Job\Payload\StringPayload('testEmptyQueue')
        );
        $validJob   = $this->bbq->fetchJob(self::QUEUE_NAME);
        $nullJob    = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this
            ->variable($validJob)->isNotNull()
            ->variable($nullJob)->isNull()
        ;
    }

    public function testLockedJobs()
    {
        $queue = $this->bbq->getQueue(self::QUEUE_NAME);
        $this
            ->object($queue)->isInstanceOf('\Eventio\BBQ\Queue\AbstractQueue')
            ->boolean($queue->hasLockedJobs())->isFalse()
        ;
        $this->bbq->pushJob(
            self::QUEUE_NAME,
            new \Eventio\BBQ\Job\Payload\StringPayload('testLockedJobs')
        );
        $this->boolean($queue->hasLockedJobs())->isFalse();

        $job = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this->boolean($queue->hasLockedJobs())->isTrue();
        $queue->releaseJob($job);
        $this->boolean($queue->hasLockedJobs())->isFalse();

        $job = $this->bbq->fetchJob(self::QUEUE_NAME);
        $this->boolean($queue->hasLockedJobs())->isTrue();
        $queue->finalizeJob($job);
        $this->boolean($queue->hasLockedJobs())->isFalse();
    }
} 