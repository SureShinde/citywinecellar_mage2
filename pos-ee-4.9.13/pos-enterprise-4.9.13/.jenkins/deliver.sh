#!/usr/bin/env sh

# Get env testing
if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-dockermftf
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-dockermftf
fi
MAGENTO_VERSION=`docker-compose -p $JOB_NAME exec -T mftf curl -s https://localhost.com/magento_version`
INFO=`docker-compose -p $JOB_NAME exec -T mftf curl -Is https://localhost.com/magento_version | grep "PHP/\|Server"`

# Notify success
curl -X POST -s --data-urlencode "payload={\"text\": \"[SUCCESS] <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME> \\n$MAGENTO_VERSION \\n$INFO\"}" $SLACK_HOOKS_POS40

# Down/Stop the containers
docker-compose -f docker-compose.report.yml -p $JOB_NAME stop allure
docker-compose -f docker-compose.report.yml -p $JOB_NAME stop nginxreport
docker-compose -f docker-compose.report.yml -p $JOB_NAME rm -f
