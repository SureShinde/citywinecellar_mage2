#!/usr/bin/env sh

echo "Pull on node $NODE_NAME"

if [[ "${IS_STABLE_TEST}" = true ]]; then
    echo "Stable Test"
fi

set -x

# Show command
set -x

git remote rm upstream
git remote add upstream https://${GIT_USERNAME}:${GIT_PASS}@github.com/Magestore/pos-enterprise
git fetch upstream
rm server/app/tests/static/Webpos/changed_files.txt
case "$JOB_NAME" in
    *pull* ) git --no-pager diff upstream/4-develop --name-only >> server/app/tests/static/Webpos/changed_files.txt;;
    * ) git --no-pager diff --name-only ${GIT_PREVIOUS_COMMIT} ${GIT_COMMIT} >> server/app/tests/static/Webpos/changed_files.txt;;
esac
#sed -i 's/server\///g' server/app/tests/static/Webpos/changed_files.txt
echo "changed list:"
cat server/app/tests/static/Webpos/changed_files.txt

rm client/pos/changed_files.txt
grep client\/pos\/.*.js$ server/app/tests/static/Webpos/changed_files.txt | grep -v json >> client/pos/changed_files.txt
sed -i 's/client\/pos\///g' client/pos/changed_files.txt
echo "client changed list:"
cat client/pos/changed_files.txt

# docker-mftf
OLD_PWD=$PWD
cd ..
if [ -d "${JOB_NAME}-dockermftf" ]; then
    cd "${JOB_NAME}-dockermftf"
    git fetch --depth 1 origin "${TEST_ENV}"
    git reset --hard HEAD
    git checkout FETCH_HEAD
else
    git clone -b "${TEST_ENV}" --depth 1 https://github.com/Magestore/docker-mftf ${JOB_NAME}-dockermftf
    cd "${JOB_NAME}-dockermftf"
fi

# Delete and backup node_modules in project space
[ ! -d "$PWD/client/pos" ] || mkdir -p $PWD/client_tmp/pos
[ ! -d "$PWD/client/pos/node_modules" ] || mv $PWD/client/pos/node_modules $PWD/client_tmp/pos/
[ ! -f "$PWD/client/pos/package-lock.json" ] || mv $PWD/client/pos/package-lock.json $PWD/client_tmp/pos/
[ ! -d "$PWD/client" ] || rm -rf $PWD/client
# Copy client to project space to build
cp -Rf $WORKSPACE/client $PWD/
# restore backup
[ ! -d "$PWD/client_tmp/pos/node_modules" ] || mv $PWD/client_tmp/pos/node_modules $PWD/client/pos/
[ ! -f "$PWD/client_tmp/pos/package-lock.json" ] || mv $PWD/client_tmp/pos/package-lock.json $PWD/client/pos/
[ ! -d "$PWD/client_tmp" ] || rm -rf $PWD/client_tmp

cd $OLD_PWD

#docker code standard
cd ..
if [[ -d "${JOB_NAME}-code-standard" ]]; then
    cd "${JOB_NAME}-code-standard"
    git fetch --depth 1 origin "${TEST_CODE_STANDARD_ENV}"
    git reset --hard HEAD
    git checkout FETCH_HEAD
else
    git clone -b "${TEST_CODE_STANDARD_ENV}" --depth 1 https://github.com/Magestore/docker-magento ${JOB_NAME}-code-standard
    cd "${JOB_NAME}-code-standard"
fi

cd $OLD_PWD

# pwa-pos server
rm -rf ../${JOB_NAME}-dockermftf/app/code/Magestore/*
cp -Rf server/app/code/Magestore ../${JOB_NAME}-dockermftf/app/code/

# add module POS Sample Data
cp -Rf server/app/tests/Magestore ../${JOB_NAME}-dockermftf/app/code/

# pwa-pos testcase
rm -rf ../${JOB_NAME}-dockermftf/app/tests/acceptance
cp -Rf server/app/tests/acceptance ../${JOB_NAME}-dockermftf/app/tests/

# pwa-pos api test
rm -rf ../${JOB_NAME}-dockermftf/app/tests/api-functional
cp -Rf server/app/tests/api-functional ../${JOB_NAME}-dockermftf/app/tests/

# pwa-pos coding standard test
rm -rf ../${JOB_NAME}-code-standard/server/*
cp -Rf server ../${JOB_NAME}-code-standard/

# fix permission
chmod -R 777 ../${JOB_NAME}-dockermftf/app/tests ../${JOB_NAME}-dockermftf/app/code/Magestore ../${JOB_NAME}-code-standard/server
