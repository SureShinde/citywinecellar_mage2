#!/usr/bin/env sh

echo "Running on node $NODE_NAME"

set -x

# Change to project directory
if [[ -z "${JENKINS_DATA}" ]]; then
    cd ../${JOB_NAME}-dockermftf
else
    cd $JENKINS_DATA/workspace/${JOB_NAME}-dockermftf
fi

CHANGED_LIST=`sed ':a;N;$!ba;s/\n/ /g' $PWD/client/pos/changed_files.txt`

# Run test
if [[ ! -z "${CHANGED_LIST}" ]]; then
    docker run --rm -i -v $PWD/client/pos:/pos -w "/pos" -e "CI=true" node:10.15 sh -c "yarn install; ./node_modules/.bin/eslint $CHANGED_LIST"
    TEST_RESULT=$?
    # Send info (hide slack key)
    set +x
    if [ $TEST_RESULT -ne 0 ]; then
        #bin/run down
        curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] $STAGE_NAME $TEST_ENV <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
        exit $TEST_RESULT
    fi
fi;

