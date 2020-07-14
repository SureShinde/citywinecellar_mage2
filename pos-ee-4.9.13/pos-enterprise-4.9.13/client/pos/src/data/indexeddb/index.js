import Dexie from 'dexie';

export {default as IndexedDbSync} from './IndexedDbSync';
export {default as IndexedDbProduct} from './IndexedDbProduct';
export {default as IndexedDbActionLog} from './IndexedDbActionLog';
export {default as IndexedDbPayment} from './IndexedDbPayment';
export {default as IndexedDbShipping} from './IndexedDbShipping';
export {default as IndexedDbOrder} from './IndexedDbOrder';
export {default as IndexedDbStock} from './IndexedDbStock';
export {default as IndexedDbErrorLog} from './IndexedDbErrorLog';
export {default as IndexedDbCustomer} from './IndexedDbCustomer';
export {default as IndexedDbCategory} from './IndexedDbCategory';
export {default as IndexedDbTaxRate} from './IndexedDbTaxRate';
export {default as IndexedDbTaxRule} from './IndexedDbTaxRule';
export {default as IndexedDbSession} from './IndexedDbSession';
export {default as IndexedDbCatalogRuleProductPrice} from './IndexedDbCatalogRuleProductPrice';

let db = new Dexie("omc_webpos");

db.version(1).stores({
    product: 'id, sku, name, pos_barcode',
    sync: 'type',
    action_log: 'action_id, uuid, order, staff_id, created_at',
    payment: 'code, sort_order',
    shipping: 'code',
    order: 'increment_id, created_at, state',
    stock: 'item_id, product_id',
    error_log: 'action_id, uuid, order, staff_id, created_at',
    customer: 'id, email, full_name',
    category: 'id, name',
    cart: 'id, pos_id, count',
    session: 'shift_increment_id, status, opened_at, updated_at, closed_at',

    product_index: 'id',
    customer_index: 'id',
    order_index: 'id'
});

db.version(2).stores({
    product: 'id, sku, name, pos_barcode',
    sync: 'type',
    action_log: 'action_id, uuid, order, staff_id, created_at',
    payment: 'code, sort_order',
    shipping: 'code',
    order: 'increment_id, created_at, state',
    stock: 'item_id, product_id',
    error_log: 'action_id, uuid, order, staff_id, created_at',
    customer: 'id, email, full_name',
    category: 'id, name',
    cart: 'id, pos_id, count',
    session: 'shift_increment_id, status, opened_at, updated_at, closed_at',
    catalogrule_product_price: 'rule_product_price_id, product_id',

    product_index: 'id',
    customer_index: 'id',
    order_index: 'id',
    catalogrule_product_price_index: 'id'
});

db.version(3).stores({
    product: 'id, sku, name, pos_barcode',
    sync: 'type',
    action_log: 'action_id, uuid, order, staff_id, created_at',
    payment: 'code, sort_order',
    shipping: 'code',
    order: 'increment_id, created_at, state',
    stock: 'item_id, product_id',
    error_log: 'action_id, uuid, order, staff_id, created_at',
    customer: 'id, email, full_name',
    category: 'id, name, level, parent_id, path',
    cart: 'id, pos_id, count',
    session: 'shift_increment_id, status, opened_at, updated_at, closed_at',
    catalogrule_product_price: 'rule_product_price_id, product_id',

    product_index: 'id',
    customer_index: 'id',
    order_index: 'id',
    catalogrule_product_price_index: 'id'
});

db.version(4).stores({
    product: 'id, sku, name, pos_barcode',
    sync: 'type',
    action_log: 'action_id, uuid, order, staff_id, created_at',
    payment: 'code, sort_order',
    shipping: 'code',
    order: 'increment_id, created_at, state',
    stock: 'item_id, product_id',
    error_log: 'action_id, uuid, order, staff_id, created_at',
    customer: 'id, email, full_name',
    category: 'id, name, level, parent_id, path',
    cart: 'id, pos_id, count',
    session: 'shift_increment_id, status, opened_at, updated_at, closed_at',
    catalogrule_product_price: 'rule_product_price_id, product_id',

    product_index: 'id',
    customer_index: 'id',
    order_index: 'id',
});

db.version(5).stores({
    product: 'id, sku, name, pos_barcode',
    sync: 'type',
    action_log: 'action_id, uuid, order, staff_id, created_at',
    payment: 'code, sort_order',
    shipping: 'code',
    order: 'increment_id, created_at, state',
    stock: 'item_id, product_id',
    error_log: 'action_id, uuid, order, staff_id, created_at',
    customer: 'id, email, full_name, tmp_customer_id',
    category: 'id, name, level, parent_id, path',
    cart: 'id, pos_id, count',
    session: 'shift_increment_id, status, opened_at, updated_at, closed_at',
    catalogrule_product_price: 'rule_product_price_id, product_id',

    product_index: 'id',
    customer_index: 'id',
    order_index: 'id',
});

db.open();

export default db;
