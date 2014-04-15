<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */


namespace Eventio\BBQ\Queue\tests\units;

use Eventio\BBQ\Queue\tests\units\AbstractQueue;

class DirectoryQueue extends AbstractQueue
{
    const QUEUE_DIR_PREFIX = '/DirectoryQueue_';

    public function beforeTestMethod($method)
    {
        parent::beforeTestMethod($method);
        $queue_dir = TESTS_TMP . self::QUEUE_DIR_PREFIX . $method;
        mkdir($queue_dir, 0777, true);
        $this->bbq->registerQueue(
            new \Eventio\BBQ\Queue\DirectoryQueue(
                parent::QUEUE_NAME,
                $queue_dir,
                ['skip_shutdown_release' => true]
        ));
    }

    public function afterTestMethod($method)
    {
        parent::afterTestMethod($method);
        self::_rmDir(TESTS_TMP . self::QUEUE_DIR_PREFIX . $method);
    }

    private static function _rmDir($dir)
    {
        $files = array_diff(scandir($dir), ['.','..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::_rmDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
