<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */


namespace Wizacha\BBQ\Queue\tests\units;

use Aws\Sqs\SqsClient;

class SqsQueue extends \Eventio\BBQ\Queue\tests\units\AbstractQueue
{

    public function beforeTestMethod($method)
    {
        $config = include(TESTS_ROOT.'/config.php');
        $sqsClient = SqsClient::factory([
            'region' => $config['region'],
            'key' => $config['key'],
            'secret' => $config['secret'],
        ]);
        parent::beforeTestMethod($method.'toto');

        $sqsQueue = new \Wizacha\BBQ\Queue\SqsQueue(
            parent::QUEUE_NAME,
            $sqsClient,
            $config['sqs_url'],
            ['skip_shutdown_release' => true]
        );
        $this->bbq->registerQueue($sqsQueue);


        while($job = $sqsQueue->fetchJob(0)){
            $sqsQueue->finalizeJob($job);
        }

    }
}
