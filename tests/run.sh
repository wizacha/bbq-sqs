#!/bin/sh
DIR="$( cd "$( dirname "$0" )" && pwd )"
${DIR}/../vendor/bin/atoum -ncc -c ${DIR}/atoum.config.php -bf ${DIR}/bootstrap.php -d ${DIR}