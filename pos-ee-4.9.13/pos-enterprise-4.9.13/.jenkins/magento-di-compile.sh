#!/usr/bin/env sh

if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-dockermftf
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-dockermftf
fi

set -x

# Run test code standard
docker-compose -p $JOB_NAME exec -u www-data -T mftf bash -c \
    " php bin/magento setup:di:compile "
TEST_RESULT=$?

# Send info (hide slack key)
set +x
if [ $TEST_RESULT -ne 0 ]; then
    #bin/run down
    curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] $STAGE_NAME $TEST_ENV <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
    exit $TEST_RESULT
fi

