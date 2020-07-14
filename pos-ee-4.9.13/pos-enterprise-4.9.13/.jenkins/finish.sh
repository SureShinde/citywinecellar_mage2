#!/usr/bin/env sh

# Get env testing
if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-dockermftf
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-dockermftf
fi

# Down/Stop the containers
docker-compose -f docker-compose.yml -p $JOB_NAME down

docker volume prune -f
