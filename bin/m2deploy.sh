#!/bin/bash
php bin/magento maintenance:enable
rm -rf var/di/* var/generation/* generation/* generated/* var/tmp/* var/cache/* var/page_cache/* var/view_preprocessed/* var/composer_home/cache/* pub/static/adminhtml/* pub/static/frontend/*
#composer update
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento -f setup:static-content:deploy en_US
php bin/magento cache:clean
php bin/magento cache:flush
php bin/magento maintenance:disable
