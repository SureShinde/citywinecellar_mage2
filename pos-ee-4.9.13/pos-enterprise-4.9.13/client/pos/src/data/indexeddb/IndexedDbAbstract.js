import AbstractResource from '../DataAbstract';
import QueryService from '../../service/QueryService';

export default class IndexedDbAbstract extends AbstractResource {
    db = require('./index').default;
    main_table = '';
    primary_key = '';
    index_table = '';
    index_table_fields = '';
    index_fields = '';
    offline_id_prefix = '';
    default_order_by = '';
    default_order_direction = QueryService.DEFAULT_ORDER_DIRECTION;

    MAX_COUNT_OF_ITEMS_IN_AN_INDEX_ROW = 60000;

    /**
     * search in near by of index
     * @param item
     * @param index
     * @param data
     * @returns {Promise<{item: *}>}
     */
    async vicinitySearchIndex(item, index, data) {
        let result = {
            item: item
        };
        let array = item.value;
        // search before
        let i = index;
        while (i >= 0 && array[i][this.default_order_by] === data[this.default_order_by]) {
            if (array[i][this.primary_key] === data[this.primary_key]) {
                result.index = i;
                result.isFound = true;
                return result;
            }
            i--;
            if (i < 0) {
                let previousItem = await this.db[this.index_table].get(result.item.id - 1);
                if (previousItem) {
                    result.item = previousItem;
                    i = result.item.value.length - 1;
                }
            }
        }

        // search after
        result.item = item;
        array = item.value;
        i = index + 1;
        while ( i < array.length && array[i][this.default_order_by] === data[this.default_order_by] ) {
            if (array[i][this.primary_key] === data[this.primary_key]) {
                result.index = i;
                result.isFound = true;
                return result;
            }
            i++;
            if (i >= array.length) {
                let nextItem = await this.db[this.index_table].get(result.item.id + 1);
                if (nextItem) {
                    result.item = nextItem;
                    i = 0;
                }
            }
        }

        result.index = i > 0 ? i - 1 : i;
        result.isFound = false;
        return result;
    }

    /**
     * search index item
     * @param item
     * @param data
     * @returns {Promise<{item: *, isFound: boolean, index: number}|{item: *}>}
     */
    async searchIndexItem(item, data) {
        let array = item.value;
        // initial values for start, middle and end
        let start = 0;
        let stop = array.length - 1;
        let middle = Math.floor((start + stop) / 2);

        // While the middle is not what we're looking for and the list does not have a single item
        while (start < stop && array[middle][this.default_order_by] !== data[this.default_order_by]) {
            if (
                (
                    this.default_order_direction === 'ASC'
                    && array[middle][this.default_order_by] > data[this.default_order_by]
                ) || (
                    this.default_order_direction === 'DESC'
                    && array[middle][this.default_order_by] < data[this.default_order_by]
                )
            ) {
                stop = middle - 1;
            } else {
                start = middle + 1;
            }

            // recalculate middle on every iteration
            middle = Math.floor((start + stop) / 2);
        }

        if (middle < 0) middle = 0;
        if (middle >= array.length) middle = array.length - 1;
        if (array[middle][this.default_order_by] !== data[this.default_order_by]) {
            return {
                item: item,
                index: middle,
                isFound: false
            }
        }
        return await this.vicinitySearchIndex(item, middle, data);

    }

    /**
     * update index of item
     * @param data
     * @param oldData
     * @returns {Promise<void>}
     */
    async updateIndexItem(data, oldData) {
        if (!this.index_table || this.indexing || !data) {
            return;
        }
        this.indexing = true;

        oldData = oldData ? oldData : data;

        let newIndexData = {
            id: data[this.primary_key]
        };
        if (this.index_table_fields) {
            this.index_table_fields.forEach(field => {
                newIndexData[field] = data[field];
            });
        }

        let searchItem;
        let lastItem;
        await this.db[this.index_table].each(item => {
            if (searchItem) {
                return;
            }
            let lastData = item.value[item.value.length - 1];
            if (
                (
                    this.default_order_direction === 'ASC'
                    && lastData[this.default_order_by] >= oldData[this.default_order_by]
                ) || (
                    this.default_order_direction === 'DESC'
                    && lastData[this.default_order_by] <= oldData[this.default_order_by]
                )
            ) {
                searchItem = item;
            }
            lastItem = item;
        });

        if (!searchItem) {
            if (lastItem && lastItem.value.length < this.MAX_COUNT_OF_ITEMS_IN_AN_INDEX_ROW) {
                lastItem.value.push(newIndexData);
            } else {
                lastItem = {
                    id: lastItem ? lastItem.id + 1 : 1,
                    value: [newIndexData]
                };
            }
            await this.db[this.index_table].bulkPut([lastItem]);
            this.indexing = false;
            return;
        }

        if (searchItem) {
            let result = await this.searchIndexItem(searchItem, oldData);
            searchItem = result.item;
            if (result.isFound) {
                searchItem.value[result.index] = newIndexData;
            } else {
                if (
                    (
                        this.default_order_direction === 'ASC'
                        && searchItem.value[result.index][this.default_order_by] <= oldData[this.default_order_by]
                    ) || (
                        this.default_order_direction === 'DESC'
                        && searchItem.value[result.index][this.default_order_by] >= oldData[this.default_order_by]
                    )
                ) {
                    searchItem.value.splice(result.index + 1, 0, newIndexData);
                } else {
                    searchItem.value.splice(result.index , 0, newIndexData);
                }
            }
            await this.db[this.index_table].bulkPut([searchItem]);
        }
        this.indexing = false;
    }

    /**
     * Update index of items before save
     * @param listData
     * @returns {Promise<void>}
     */
    async updateIndexItemsBeforeSave(listData) {
        if (!listData || !listData.length) {
            return;
        }
        for (let i = 0; i < listData.length; i++) {
            let data = listData[i];
            let oldData = await this.db[this.main_table].get(data[this.primary_key]);
            await this.updateIndexItem(data, oldData);
        }
    }

    /**
     * check need reindex
     * @returns {Promise<*|boolean>}
     */
    async needReindex() {
        if (!this.index_table) return false;
        let needReindex = false;
        await this.db[this.index_table].each(item => {
            if (
                (item.id === 1 && item.value[0] && item.value[0][this.default_order_by] === undefined)
                || item.value.length > (this.MAX_COUNT_OF_ITEMS_IN_AN_INDEX_ROW + 10000)
            ) {
                needReindex = true;
            }
        });
        return needReindex;
    }

    /**
     * Reindex table
     */
    reindexTable() {
        if (!this.index_table || this.indexing) {
            return;
        }
        this.indexing = true;
        // Clear indexed data
        this.db[this.index_table].clear();

        // Indexing
        let table = this.db[this.main_table];
        if (this.default_order_by) {
            table = table.orderBy(this.default_order_by);
            if (this.default_order_direction !== QueryService.DEFAULT_ORDER_DIRECTION) {
                table = table.reverse();
            }
        }
        let items = [], id = 1;
        return table.each(item => {
            let indexedItem = {
                id: item[this.primary_key]
            };
            if (this.index_table_fields) {
                this.index_table_fields.forEach(field => {
                    indexedItem[field] = item[field];
                });
            }
            items.push(indexedItem);
            if (items.length >= this.MAX_COUNT_OF_ITEMS_IN_AN_INDEX_ROW) {
                // Push data to indexed table
                let data = {
                    id: id++,
                    value: items
                };
                setTimeout(() => this.db[this.index_table].add(data));
                items = [];
            }
        }).then(() => {
            if (items.length > 0) {
                this.db[this.index_table].add({
                    id: id,
                    value: items
                }).then(() => {
                    this.indexing = false;
                });
            } else {
                this.indexing = false;
            }
        });
    }

    /**
     * Add or update data in indexedDb table
     *
     * @param {object} data
     * @returns {Promise<any>}
     */
    save(data) {
        if (!data) {
            return null;
        }
        if (data[this.primary_key]) {
            return this.db[this.main_table].update(data[this.primary_key], data);
        }
        data[this.primary_key] = this.offline_id_prefix + '_' + new Date().getTime();
        return this.db[this.main_table].put(data);
    }

    /**
     * Add multiple data in indexedDb table
     *
     * @param {array} data
     * @returns {Promise<any>}
     */
    bulkAdd(data) {
        return new Promise((resolve, reject) => {
            if (!Array.isArray(data)) {
                reject(0);
            }
            this.db[this.main_table].bulkAdd(data)
                .then(lastKey => resolve(data.length))
                .catch(this.db.BulkError, error => reject(data.length - error.failures.length));
        });
    }

    /**
     * Put new data and replace old data of objects in indexedDb table
     *
     * @param {array} data
     * @param {number} requestTime
     * @returns {Promise<any>}
     */
    bulkPut(data, requestTime = 1) {
        return new Promise((resolve, reject) => {
            if (requestTime > 10) {
                resolve(0);
            }
            if (!Array.isArray(data)) {
                resolve(0);
            }
            try {
                this.db[this.main_table].bulkPut(data).then(lastKey => {
                    resolve(data.length)
                }).catch('BulkError', error => {
                    this.bulkPut(data, requestTime++)
                        .then(response => resolve(response))
                        .catch(error => resolve(error));
                }).catch('AbortError', error => {
                    this.bulkPut(data, requestTime++)
                        .then(response => resolve(response))
                        .catch(error => resolve(error));
                }).catch('TimeoutError', error => {
                    this.bulkPut(data, requestTime++)
                        .then(response => resolve(response))
                        .catch(error => resolve(error));
                }).catch(Error, error => {
                    this.bulkPut(data, requestTime++)
                        .then(response => resolve(response))
                        .catch(error => resolve(error));
                }).catch(error => {
                    this.bulkPut(data, requestTime++)
                        .then(response => resolve(response))
                        .catch(error => resolve(error));
                });
            } catch (error) {
                this.bulkPut(data, requestTime++)
                    .then(response => resolve(response))
                    .catch(error => resolve(error));
            }
        });
    }

    /**
     * Load a first item in indexedDb which is in suitable condition
     *
     * @param {string} id
     * @param {string} field
     * @returns {Promise<any>}
     */
    get(id, field = null) {
        return new Promise((resolve, reject) => {
            if (field === null) {
                field = this.primary_key;
            }
            this.db[this.main_table].where(field).equals(id).limit(1).first(item => {
                if (item) {
                    resolve(item);
                } else {
                    resolve({});
                }
            }).catch(exception => {
                reject(exception);
            });
        })
    }

    /**
     * get data by id
     * @param id
     * @return {*}
     */
    getById(id) {
        return this.db[this.main_table].get(id);
    }

    /**
     * Load a first item in indexedDb which is in suitable condition
     *
     * @param {string} id
     * @param {string} field
     * @returns {Promise<any>}
     */
    getListByIndex(ids, limit = null, field = null) {
        return new Promise((resolve, reject) => {
            if (field === null) {
                field = this.primary_key;
            }
            if (limit === null) {
                limit = 16;
            }
            this.db[this.main_table].where(field).inAnyRange(ids).limit(limit)(items => {
                if (items) {
                    resolve(items);
                } else {
                    resolve([]);
                }
            }).catch(exception => {
                reject(exception);
            });
        })
    }

    /**
     * Get all data of table
     * @returns {Promise<any>}
     */
    getAll() {
        return this.db[this.main_table].toArray();
    }

    /**
     * Clear table
     * @returns {*}
     */
    clear() {
        return this.db[this.main_table].clear();
    }

    /**
     * delete item in table
     * @param id
     */
    delete(id) {
        return this.db[this.main_table].delete(id);
    }

    /**
     * bulk delete items
     *
     * @param {array} ids
     */
    bulkDelete(ids) {
        return this.db[this.main_table].bulkDelete(ids);
    }

    /**
     * Filter an item with a condition
     *
     * @param {object} item
     * @param {object} filter
     * @return {boolean}
     */
    filterOne(item, filter) {
        let meetFilter = false;
        if (filter.condition === 'like') {
            filter.value = filter.value.replace('%', '').replace('%', '');
            if (String(item[filter.field]).toLowerCase().indexOf(String(filter.value).toLowerCase()) >= 0) {
                meetFilter = true;
            }
        } else if (filter.condition === 'eq') {
            if (String(item[filter.field]) === String(filter.value)) {
                meetFilter = true;
            }
        } else if (filter.condition === 'neq') {
            if (String(item[filter.field]) !== String(filter.value)) {
                meetFilter = true;
            }
        } else if (filter.condition === 'gt') {
            if (item[filter.field] > filter.value) {
                meetFilter = true;
            }
        } else if (filter.condition === 'lt') {
            if (item[filter.field] < filter.value) {
                meetFilter = true;
            }
        } else if (filter.condition === 'gteq') {
            if (item[filter.field] >= filter.value) {
                meetFilter = true;
            }
        } else if (filter.condition === 'lteq') {
            if (item[filter.field] <= filter.value) {
                meetFilter = true;
            }
        } else if (filter.condition === 'in') {
            if (Array.isArray(filter.value) && filter.value.indexOf(item[filter.field]) >= 0) {
                meetFilter = true;
            }
        } else if (filter.condition === 'nin') {
            if (Array.isArray(filter.value) && filter.value.indexOf(item[filter.field]) < 0) {
                meetFilter = true;
            }
        }
        return meetFilter;
    }

    /**
     * Filter an item with filter params
     *
     * @param {object} item
     * @param {object} query
     * @return {boolean}
     */
    filter(item, query) {
        let meetFilter = true;
        if (query.queryString) {
            meetFilter = false;
            query.queryString = query.queryString.replace(/%/g, "");
            if (String(item.search_string).toLowerCase().indexOf(String(query.queryString).toLowerCase()) >= 0) {
                meetFilter = true;
            }
        }
        if (query.filterParams.length > 0) {
            query.filterParams.map(filterParam => {
                if (!meetFilter) {
                    return false;
                }
                meetFilter = this.filterOne(item, filterParam);
                return filterParam;
            });
        }
        if (!meetFilter) {
            return false;
        }

        if (query.orFilterParams.length > 0) {
            query.orFilterParams.map(filterParams => {
                if (!meetFilter) {
                    return false;
                }
                meetFilter = false;
                filterParams.map(filter => {
                    if (meetFilter) {
                        return true;
                    }
                    meetFilter = this.filterOne(item, filter);
                    return false;
                });
                return filterParams;
            });
        }
        return meetFilter;
    }

    /**
     * Sort all filtered items
     *
     * @param {array} items
     * @param {array} orderParams
     * @return {*}
     */
    sort(items, orderParams) {
        orderParams.map(value => {
            if (value.direction !== QueryService.DEFAULT_ORDER_DIRECTION) {
                items.sort(function (a, b) {
                    let x = a[value.field];
                    let y = b[value.field];
                    if (typeof x === "string") {
                        x = x.toLowerCase();
                    }
                    if (typeof y === "string") {
                        y = y.toLowerCase();
                    }
                    return ((x > y) ? -1 : ((x < y) ? 1 : 0));
                });
            } else {
                items.sort(function (a, b) {
                    let x = a[value.field];
                    let y = b[value.field];
                    if (typeof x === "string") {
                        x = x.toLowerCase();
                    }
                    if (typeof y === "string") {
                        y = y.toLowerCase();
                    }
                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                });
            }
            return value;
        });
        return items;
    }

    /**
     * Get list item in indexed DB with QueryService object
     *
     * @param {object} queryService
     * @return {Promise<any>}
     */
    getList(queryService = {}) {
        let query = Object.assign({}, queryService);
        let table = this.db[this.main_table];
        let total = 0;
        // let cacheKey = JSON.stringify(query.filterParams) + JSON.stringify(query.paramOrFilter);
        // use cache in next sprint
        let cacheKey = Date.now();
        if (this.index_table && !this.indexing) {
            return this.searchIndex(query, cacheKey);
        } else {
            return this.getListByQuery(query, table, total);
        }

    }

    /**
     * get list normally
     *
     * @param queryService
     * @param query
     * @param table
     * @param total
     * @returns {Promise<any>}
     */
    getListByQuery(query, table, total) {
        return new Promise((resolve) => {
            table = table.toCollection();
            if (query.orFilterParams.length > 0 || query.filterParams.length > 0 || query.queryString) {
                table.filter(item => this.filter(item, query));
            }
            return table.toArray(items =>
                this.sort(items, query.orderParams)
            ).then(items => {
                total = items.length;
                if (query.pageSize) {
                    let from = (query.currentPage - 1) * query.pageSize,
                        to = query.currentPage * query.pageSize - 1;
                    if (items.length - 1 < to) {
                        to = items.length - 1;
                    }
                    if (from === to) {
                        items = items.slice(from);
                    } else {
                        items = items.slice(from, to + 1);
                    }
                }
                resolve({
                    items: items,
                    search_criteria: {
                        page_size: query.pageSize,
                        current_page: query.currentPage
                    },
                    total_count: total
                })
            }).catch(error => {
                resolve({
                    items: [],
                    search_criteria: {
                        page_size: query.pageSize,
                        current_page: query.currentPage
                    },
                    total_count: 0
                })
            });
        });
    }

    /**
     * Search from index table
     *
     * @param filter
     * @param pageSize
     * @param currentPage
     * @param cacheKey
     * @param orderParams
     * @returns {*}
     */
    searchIndex(query, cacheKey) {
        return new Promise((resolve, reject) => {
            let result = [],
                total = 0,
                pageSize = query.pageSize,
                // orderParams = query.orderParams, // Use default_order_by
                currentPage = query.currentPage ? query.currentPage : 1,
                self = this;
            // Start search
            this.db[self.index_table].each(items => {
                if (!items) {
                    return;
                }
                let res = items.value;
                for (let i = 0, n = res.length; i < n; i++) {
                    let item = res[i];
                    if (self.filter(item, query)) {
                        total++;
                        result.push(item.id);
                    }
                }
            }).then(() => {
                self.getIdsFromIndexTable(result, total, cacheKey, null, pageSize, currentPage)
                    .then(data => {
                        if (0 === data.result.length) {
                            resolve({
                                items: [],
                                search_criteria: {
                                    page_size: query.pageSize,
                                    current_page: query.currentPage
                                },
                                total_count: data.total
                            });
                        }
                        // Load Real Data
                        let ordered = data.result,
                            range = ordered.slice(0).sort(function (a, b) {
                                return a - b;
                            });
                        this.db[self.main_table].where(self.primary_key).anyOf(range).toArray(items => {
                            if (!items) {
                                return;
                            }
                            this.sort(items, query.orderParams);
                            resolve({
                                items: items,
                                search_criteria: {
                                    page_size: query.pageSize,
                                    current_page: query.currentPage
                                },
                                total_count: data.total
                            });
                        })
                            .catch(function (err) {
                                return reject(err);
                            });
                    });
            }).catch(function (err) {
                return reject(err);
            });
        })
    }

    /**
     * get not existed ids
     *
     * @param {Array} ids
     * @returns {Promise}
     */
    getNotExistedIds(ids) {
        return new Promise((resolve, reject) => {
            this.db[this.main_table].where('id').anyOf(ids)
                .keys(existedIds => resolve(ids.filter(id => -1 === existedIds.indexOf(id))))
                .catch(err => reject(err));
        });
    }

    /**
     * Get Ids from index table
     *
     * @param cacheKey
     * @param orderParams
     * @param pageSize
     * @param currentPage
     * @returns {Promise<any>}
     */
    getIdsFromIndexTable(result, total, cacheKey, orderParams, pageSize, currentPage) {
        return new Promise((resolve) => {
            let self = this;
            result.cacheKey = cacheKey;
            // $[self.index_table] = result;
            if (orderParams && result.length && typeof result[0] === 'object') {
                orderParams.forEach((value) => {
                    if (value.direction === 'DESC') {
                        result.sort(function (a, b) {
                            let x = a[value.field];
                            let y = b[value.field];
                            if (typeof x === "string") {
                                x = x.toLowerCase();
                            }
                            if (typeof y === "string") {
                                y = y.toLowerCase();
                            }
                            return ((x > y) ? -1 : ((x < y) ? 1 : 0));
                        });
                    } else {
                        result.sort(function (a, b) {
                            let x = a[value.field];
                            let y = b[value.field];
                            if (typeof x === "string") {
                                x = x.toLowerCase();
                            }
                            if (typeof y === "string") {
                                y = y.toLowerCase();
                            }
                            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                        });
                    }
                });
                result = self.sort(result, orderParams);
                for (let i = result.length - 1; i >= 0; i--) {
                    result[i] = result[i].id;
                }
            }
            // Resolve Result
            if (pageSize) {
                let from = (currentPage - 1) * pageSize,
                    to = currentPage * pageSize - 1;
                if (result.length - 1 < to) {
                    to = result.length - 1;
                }
                if (from === to) {
                    result = result.slice(from);
                } else {
                    result = result.slice(from, to + 1);
                }
            }
            resolve({
                result: result,
                total: total,
            });
        });
    }
}
