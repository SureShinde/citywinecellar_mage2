#!/usr/bin/env sh
echo "Running on node $NODE_NAME"

set -x

# Change to project directory
if [[ -z "${JENKINS_DATA}" ]]; then
    cd "../${JOB_NAME}-dockermftf"
else
    cd "$JENKINS_DATA/workspace/${JOB_NAME}-dockermftf"
fi
echo "Current dir: `pwd`"

# Run build
docker run --rm -i -v $PWD/client/pos:/pos -w "/pos" -e "CI=true" node:10.15 sh -c "yarn install; yarn run build; exit $?"

if [ $? -eq 0 ]; then
    mkdir -p app/code/Magestore/Webpos/build/apps
    rm -Rf app/code/Magestore/Webpos/build/apps/pos
    cp -Rf client/pos/build app/code/Magestore/Webpos/build/apps/pos
    rm -Rf pub/apps/pos # remove old pwapos
else
    set +x
    curl -X POST -s --data-urlencode "payload={\"text\": \"[FAILURE] $STAGE_NAME <$RUN_DISPLAY_URL|$JOB_NAME $BUILD_DISPLAY_NAME>\"}" $SLACK_HOOKS_POS40
    exit 1
fi

# Move Magestore modules to document root to disable installing with magento
if [ -d "app/code/Magestore" ]; then
    rm -rf Magestore_modules
    mv app/code/Magestore Magestore_modules
fi

# Start services
COMPOSE_HTTP_TIMEOUT=200 bin/run -p $JOB_NAME up -d

# Waiting for services is up
set -x

# check db container is run correctly
WHILE_LIMIT=10 # timeout 360 seconds
while ! DBISUP=`docker-compose -p $JOB_NAME ps | grep 3306 | grep Up`
do
    if [ ! -z "$DBISUP" ]; then
        break
    else
        docker-compose -p $JOB_NAME rm db # remove stopped container
        COMPOSE_HTTP_TIMEOUT=200 bin/run -p $JOB_NAME up -d
        if [ $WHILE_LIMIT -lt 1 ]; then
            break
        fi
    fi
    WHILE_LIMIT=$(( WHILE_LIMIT - 1 ))
    sleep 3
done

# wait for db is up
COUNT_OUT_LIMIT=120 # timeout 360 seconds
while ! RESPONSE=`docker-compose -p $JOB_NAME exec -T functional curl -s https://localhost.com/magento_version`
do
    if [ $COUNT_OUT_LIMIT -lt 1 ]; then
        break
    fi
    COUNT_OUT_LIMIT=$(( COUNT_OUT_LIMIT - 1 ))
    sleep 3
done

# recheck and wait for db is up
if [[ ${RESPONSE:0:8} != "Magento/" ]]; then
    COMPOSE_HTTP_TIMEOUT=200 docker-compose -p $JOB_NAME restart mftf
    RETRY_LIMIT=1 # retry 1 loop
    COUNT_OUT_LIMIT=100 # timeout 300 seconds
    while ! docker-compose -p $JOB_NAME exec -T functional curl -s https://localhost.com/magento_version
    do
        COUNT_OUT_LIMIT=$(( COUNT_OUT_LIMIT - 1 ))
        if [ $COUNT_OUT_LIMIT -lt 1 ]; then
            # if database cannot start or error try to restart it
            if [ -z "$(docker-compose -p $JOB_NAME ps | grep 3306 | grep Up)" ]; then
                docker-compose -p $JOB_NAME rm db # remove stopped container
                COMPOSE_HTTP_TIMEOUT=200 bin/run -p $JOB_NAME up -d
                COUNT_OUT_LIMIT=100
                RETRY_LIMIT=$(( RETRY_LIMIT - 1 ))
            fi
            if [ $RETRY_LIMIT -lt 1 ]; then
                echo "Error with db logs:"
                docker-compose -p $JOB_NAME logs db
                exit 1
            fi
        fi
        sleep 3
    done
fi

set -x

# configs magento
# Set admin logout time limit
# Set disable static sign
docker-compose -p $JOB_NAME exec -u www-data -T mftf bash -c \
    "php bin/magento config:set dev/static/sign 0 ; \
    php bin/magento cache:clean config"
# docker-compose -p $JOB_NAME exec -u www-data -T mftf bash -c \
#     "php bin/magento config:set admin/security/lockout_threshold 300 ; \
#     php bin/magento config:set admin/security/session_lifetime 9000 ; \
#     php bin/magento cache:clean config"


# Install webpos payment library
if [ -d "Magestore_modules" ]; then
    if [ -d "app/code/Magestore" ]; then
        cp -rpf Magestore_modules/* app/code/Magestore
        rm -rf Magestore_modules/
    else
        mv Magestore_modules app/code/Magestore
    fi
fi
chmod -R 777 app/.composer
docker-compose -p $JOB_NAME exec -u www-data -T mftf bash -c \
    "php bin/magento setup:upgrade ; \
    php bin/magento webpos:deploy ; \
    php bin/magento setup:static-content:deploy -f \
    "

if [[ "${IS_STABLE_TEST}" = true ]]; then
    docker-compose -p $JOB_NAME exec -u www-data -T mftf bash -c \
        "php bin/magento webpos:generate:product ; \
        php bin/magento webpos:generate:customer ; \
        php bin/magento webpos:generate:order"
fi
