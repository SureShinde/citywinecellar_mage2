import config from '../extension/config'

export class Extension {
  ExtensionConfig = config();
  generatedClasses = {};

  /**
   * get rewrite class if available or itself class
   * if class has plugin method, we call them
   *
   * @param {String} type
   * @param {Class} Class
   * @return {Class}
   */
  get(type, Class) {
    let className = Class.className;
    if (!className) {
      return Class;
    }
    if (this.generatedClasses[className]) {
      return this.generatedClasses[className];
    }
    Class = this.getMixinClass(type, Class);
    if (
      !this.ExtensionConfig
      || !this.ExtensionConfig.plugin
      || !this.ExtensionConfig.plugin[type]
      || !this.ExtensionConfig.plugin[type][className]
    ) {
      this.generatedClasses[className] = Class;
      return Class;
    }
    let plugin = this.ExtensionConfig.plugin[type][className];
    for (let method in plugin) {
      if (plugin.hasOwnProperty(method)) {
        let configs = Object.keys(plugin[method])
          .map(name => plugin[method][name])
          .filter(config => !config.disabled)
          .sort((a, b) => b.sortOrder - a.sortOrder);
        let pluginMethod = Class.prototype[method] ? Class.prototype[method] : Class[method];
        pluginMethod = configs.reduce((fn, config) => {
          return function(...args) {
            let result;
            if (config.hasOwnProperty('before')) {
              let before = config.before.call(this, ...args);
              if (null !== before && undefined !== before) {
                if (1 === args.length || !Array.isArray(before)) {
                  args = [before];
                } else {
                  args = before;
                }
              }
            }
            if (config.hasOwnProperty('around')) {
              result = config.around.call(this, fn.bind(this), ...args);
            } else {
              result = fn.call(this, ...args);
            }
            if (config.hasOwnProperty('after')) {
              result = config.after.call(this, result, ...args);
            }
            return result;
          };
        }, pluginMethod);
        if (!Class.prototype[method]) {
          Class[method] = pluginMethod;
        } else {
          Class.prototype[method] = pluginMethod;
        }
      }
    }
    this.generatedClasses[className] = Class;
    return Class;
  }

  /**
   * get mixined class if available
   *
   * @param {String} type
   * @param {Class} Class
   * @returns {Class}
   */
  getMixinClass(type, Class) {
    let className = Class.className;
    if (!className) {
      return Class;
    }
    Class = this.getClass(type, Class);
    if (
      !this.ExtensionConfig
      || !this.ExtensionConfig.mixin
      || !this.ExtensionConfig.mixin[type]
    ) {
      return Class;
    }

    if (this.ExtensionConfig.mixin[type][className]) {
      let mixin = this.ExtensionConfig.mixin[type][className];
      for (const method in mixin) {
        if (mixin.hasOwnProperty(method)) {
          const mixinMethod = mixin[method];
          if (method === 'static') {
            for (const staticMethod in mixinMethod) {
              if (mixinMethod.hasOwnProperty(staticMethod)) {
                Class[staticMethod] = mixinMethod[staticMethod];
              }
            }
          } else {
            Class.prototype[method] = mixinMethod;
          }
        }
      }
    }
    return Class;
  }

  /**
   * get rewrite class if available or itself class
   *
   * @param {String} type
   * @param {Class} Class
   * @returns {Class}
   */
  getClass(type, Class) {
    if (
      !this.ExtensionConfig
      || !this.ExtensionConfig.rewrite
      || !this.ExtensionConfig.rewrite[type]
    ) {
      return Class;
    }

    let className = Class.className;
    if (!className) {
      return Class;
    }
    if (this.ExtensionConfig.rewrite[type][className]) {
      return this.ExtensionConfig.rewrite[type][className](Class);
    }
    return Class;
  }
}

let extension = new Extension();

export default extension;
