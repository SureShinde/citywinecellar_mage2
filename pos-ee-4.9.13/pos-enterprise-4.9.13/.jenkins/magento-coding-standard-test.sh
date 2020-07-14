#!/usr/bin/env sh

echo "Running on node $NODE_NAME"

# Build test api phpunit_pwapos.xml
PWAPOS_API_TESTSUITE="PWAPOS"

if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-code-standard
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-code-standard
fi

# Start services
COMPOSE_HTTP_TIMEOUT=200 docker-compose up -d

# Waiting for services is up
set -x
while ! RESPONSE=`docker-compose exec -T magento php bin/magento --version`
do
    sleep 3
done

if [[ ${RESPONSE:0:7} != "Magento" ]]; then
    COMPOSE_HTTP_TIMEOUT=200 docker-compose restart magento
    while ! docker-compose exec -T magento php bin/magento --version
    do
        sleep 3
    done
fi

set -x
#exit 0 #NOTE: fake success
# Run tests
docker-compose exec -u www-data -T magento bash -c \
    "php bin/magento dev:test:run static -c'--testsuite=$PWAPOS_API_TESTSUITE' ; "
TEST_RESULT=$?

docker-compose down

# Send info (hide slack key)
set +x

if [ $TEST_RESULT -ne 0 ]; then
    #bin/run down
    curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] $STAGE_NAME $TEST_ENV <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
    exit $TEST_RESULT
fi

