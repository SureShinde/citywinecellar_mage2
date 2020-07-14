#!/usr/bin/env sh

echo "Running on node $NODE_NAME"

set -x

# Change to project directory
if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-dockermftf
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-dockermftf
fi

# Run test
docker run --rm -i -v $PWD/client/pos:/pos -w "/pos" -e "CI=true" node:10.15 sh -c "yarn install; yarn run test -- --coverage"
TEST_RESULT=$?

# Copy unit test report
mkdir -p app/allure/allure-report/client
cp -Rf client/pos/coverage/lcov-report/* app/allure/allure-report/client/

# Show report
docker-compose -p $JOB_NAME -f docker-compose.report.yml up -d --force-recreate
#REPORT_URL=`echo $JENKINS_URL | awk 'gsub(/\:?[0-9]*\/?$/, "")'`

# Detect exposed port
sleep 2
PORT=`docker-compose -p $JOB_NAME -f docker-compose.report.yml port --protocol=tcp nginxreport 80 | sed 's/0.0.0.0://'`
#REPORT_URL="$REPORT_URL:$PORT/client/"
REPORT_URL="http://$NODE_IP:$PORT/client/"

set -x
echo "View report at: $REPORT_URL"

# Send info (hide slack key)
set +x
if [ $TEST_RESULT -ne 0 ]; then
    curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] <$REPORT_URL|$STAGE_NAME> $TEST_ENV <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
    exit $TEST_RESULT
fi

