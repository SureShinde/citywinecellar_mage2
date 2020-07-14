import config from '../extension/config'

/**
 * Empty function pointer
 */
function empty() {}

/**
 * Compile config with function call
 *
 * @param {any} config
 * @returns {any}
 */
function compile(config) {
  if (!Array.isArray(config)
    || undefined === config.find(e => 'function' === typeof e)
  ) {
    return config;
  }
  return function(...args) {
    return config.map(e => ('function' === typeof e) ? e.apply(null, args) : e);
  };
}

/**
 * Get layout function by config
 *
 * @param {Object} config
 * @returns {Function}
 */
function getLayout(config) {
  return function (name) {
    if (!name) {
      return (undefined === config) ? empty : compile(config);
    } else {
      return getLayout((!config || !config[name]) ? undefined : config[name]);
    }
  }
}

let cache = {};

/**
 * Get cache object
 *
 * @returns {Object}
 */
function getCache() {
  if (cache.__config !== config()) {
    cache = {__config: config()};
  }
  return cache;
}

/**
 * Get layout function config by name
 *
 * Name of layout can be dot(.) pattern:
 *  layout('product.category.list') = layout('product')('category')('list')
 *
 * @param {String} name
 * @returns {Function}
 */
export default function layout(name) {
  let cache = getCache();
  if (!cache.hasOwnProperty(name)) {
    cache[name] = getLayout(name.split('.').reduce(function(config, name) {
      return (!config || !config[name]) ? undefined : config[name];
    }, config().layout));
  }
  return cache[name];
}
