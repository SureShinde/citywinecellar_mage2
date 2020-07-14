#!/usr/bin/env sh

echo "Running on node $NODE_NAME"

# Build test api phpunit_pwapos.xml
PWAPOS_API_TESTSUITE="PWAPOS_API"
PHPUNIT_PWAPOS_XML_FILE=phpunit_pwapos.xml

if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-dockermftf
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-dockermftf
fi

set -x
# exit 0 #NOTE: fake success
# Run tests
docker-compose -p $JOB_NAME exec -u www-data -T mftf bash -c \
    "cd dev/tests/api-functional; \
    cp testsuite/Magento/Webpos/$PHPUNIT_PWAPOS_XML_FILE .; \
    php ../../../vendor/phpunit/phpunit/phpunit -c /var/www/html/dev/tests/api-functional/$PHPUNIT_PWAPOS_XML_FILE --testsuite $PWAPOS_API_TESTSUITE; "
TEST_RESULT=$?


# Send info (hide slack key)
set +x

if [ $TEST_RESULT -ne 0 ]; then
    #bin/run down
    curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] $STAGE_NAME $TEST_ENV <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
    exit $TEST_RESULT
fi

