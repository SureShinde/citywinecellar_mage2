require('jest-localstorage-mock');
import Config from "./config/Config";
Config.platform = 'Omc';
Config.db = 'IndexedDb';

const Dexie = require('dexie');

Dexie.dependencies.indexedDB = require('fake-indexeddb');
Dexie.dependencies.IDBKeyRange = require('fake-indexeddb/lib/FDBKeyRange');
