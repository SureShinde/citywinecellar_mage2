import Abstract from './IndexedDbAbstract';

export default class IndexedDbCustomer extends Abstract {
    static className = 'IndexedDbCustomer';

    main_table = 'customer';
    primary_key = 'id';
    offline_id_prefix = 'customer';
    index_table = 'customer_index';
    index_table_fields= [
        'id',
        'full_name',
        'search_string',
    ];
    default_order_by = 'full_name';

    /**
     * check email
     * @param email
     * @returns {Dexie.Promise<any | T> | Dexie.Promise<any>}
     */
    checkEmail(email) {
        return this.db[this.main_table].where('email').equalsIgnoreCase(email).first(item => !item);
    }

    /**
     * update customer
     * @param data
     * @returns {Promise}
     */
    updateCustomer(data) {
        return new Promise((resolve, reject) => {
            this.db[this.main_table].where('id').equals(data.id).or('email').equalsIgnoreCase(data.email).delete().then(() => {
                this.bulkPut([data]).then(
                    item => {
                        let new_search_string = data.email + data.full_name;
                        if (new_search_string !== data.search_string) {
                            this.reindexTable();
                        }
                        resolve(data);
                    }
                );
            });
        })
    }
}
