#!/usr/bin/env sh

if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-dockermftf
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-dockermftf
fi

set -x

# Run test code standard
docker run --rm -i -u www-data -v $(pwd)/app/code:/var/www/html/app/code magestore/magento-standard bash -c \
    "cd magento-coding-standard ; \
    vendor/bin/phpcs ../app/code/Magestore --standard=MEQP2 --severity=10"
TEST_RESULT=$?

# Send info (hide slack key)
set +x
if [ $TEST_RESULT -ne 0 ]; then
    #bin/run down
    curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] $STAGE_NAME $TEST_ENV <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
    exit $TEST_RESULT
fi

