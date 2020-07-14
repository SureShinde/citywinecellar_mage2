import Abstract from './IndexedDbAbstract';

export default class IndexedDbCart extends Abstract {
    static className = 'IndexedDbCart';
    main_table = 'cart';
    primary_key = 'id';


    /**
     * Search by pos Id
     *
     * @param posId
     * @returns {Promise<any>}
     */
    searchByPosId(posId) {
        return new Promise((resolve, reject) => {
            this.db[this.main_table]
                .where('pos_id')
                .equalsIgnoreCase(posId)
                .reverse()
                .sortBy('id')
                .then(items => resolve(items))
                .catch(() => reject([]));
        });
    }

    /**
     * add new record
     * @param data
     * @param requestTime
     */
    add(data, requestTime = 1) {
        return new Promise((resolve, reject) => {
            if (requestTime > 10) {
                resolve(0);
            }
            this.db[this.main_table].put(data).then(response => {
                resolve(response);
            }).catch('AbortError', error => {
                this.add(data, requestTime++)
                    .then(response => resolve(response))
                    .catch(error => resolve(error));
            }).catch('TimeoutError', error => {
                this.bulkPut(data, requestTime++)
                    .then(response => resolve(response))
                    .catch(error => resolve(error));
            }).catch(error => {
                this.add(data, requestTime++)
                    .then(response => resolve(response))
                    .catch(error => resolve(error));
            })
        });
    }
}
