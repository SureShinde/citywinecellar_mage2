#!/usr/bin/env sh

echo "Testing on node $NODE_NAME"

set -x

if [[ -z "${JENKINS_DATA}" ]]; then
    cd "../${JOB_NAME}-dockermftf"
else
    cd "$JENKINS_DATA/workspace/${JOB_NAME}-dockermftf"
fi

set -x

TEST_GROUP="pwapos"
if [[ "${IS_STABLE_TEST}" = true ]]; then
    TEST_GROUP="Stable"
fi

# Run tests
# Use of robo will be deprecated with next major release, please use <root>/vendor/bin/mftf generate:tests
docker-compose -p $JOB_NAME exec -u www-data -T functional bash -c \
    "vendor/bin/mftf run:group $TEST_GROUP"
    # " vendor/bin/mftf generate:tests; \
    # cd dev/tests/acceptance; \
    # vendor/bin/codecept run functional --group pwapos"

TEST_RESULT=$?

# Copy test results to allure report
docker-compose -p $JOB_NAME exec -T functional bash -c \
    "rm -rf /allure-results/*; \
    cp dev/tests/acceptance/tests/_output/allure-results/* /allure-results/ ; \
    chmod -R 777 /allure-results/"

# pause to debug
#while true; do sleep 3; echo "Pause"; done

# start report server
docker-compose -f docker-compose.report.yml -p $JOB_NAME up -d --force-recreate
#REPORT_URL=`echo $JENKINS_URL | awk 'gsub(/\:?[0-9]*\/?$/, "")'`

# detect exposed port
sleep 2
#IP=`ip route|awk '/0\/24/ { print $9 }'`
PORT=`docker-compose -p $JOB_NAME -f docker-compose.report.yml port --protocol=tcp allure 4040 | sed 's/0.0.0.0://'`
REPORT_URL="http://$NODE_IP:$PORT/"

set -x
echo "View report at: $REPORT_URL"

# Send info (hide slack key)
set +x
if [ $TEST_RESULT -ne 0 ]; then
    # Down/Stop the containers
    #bin/run -p $JOB_NAME down
    curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] <$REPORT_URL|$STAGE_NAME> $TEST_ENV <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
    exit $TEST_RESULT
fi
