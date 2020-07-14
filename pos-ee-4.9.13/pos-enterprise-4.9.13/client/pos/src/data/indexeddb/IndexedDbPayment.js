import Abstract from './IndexedDbAbstract';

export default class IndexedDbPayment extends Abstract {
    static className = 'IndexedDbPayment';

    main_table = 'payment';
    primary_key = 'code';
    offline_id_prefix = '';

    /**
     * Get all data of table
     * @returns {Promise<any>}
     */
    getAll() {
        return this.db[this.main_table].toCollection().sortBy('sort_order');
    }
}
