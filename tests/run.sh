#!/bin/sh
DIR="$( cd "$( dirname "$0" )" && pwd )"
mkdir ${DIR}/logs
${DIR}/../vendor/bin/atoum -mcn 1 -ncc -c ${DIR}/atoum.config.php -bf ${DIR}/bootstrap.php -d ${DIR}