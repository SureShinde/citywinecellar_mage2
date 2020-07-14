# Mixin Mechanism

A module can add a method to a class from `core` or `other module`.

## Config Mixin
In the module, we register a mixin in file `etc/config.js`.

For example: We add mixin for MenuComponent
```js
import ModuleConfigAbstract from "../../ModuleConfigAbstract";

class HelloWorldConfig extends ModuleConfigAbstract{
    module = ['helloworld'];
    mixin = {
        component: {
            MenuComponent: {
                // new method name: mixin method implementation
                setOrder: function(order) {
                    this.order = order;
                    return this;
                },
                getOrder: function() {
                    return this.order;
                },
                // Static methods
                static: {
                    plus: function(a, b) {
                        return b + a;
                    }
                }
            }
        }
    };
}

export default (new HelloWorldConfig());
```

We have 7 type of class can be plugged in:

1. service
2. resource_model
3. repository
4. epic
5. container
6. component
7. data_resource

To determine which type of class, we can find `Factory` type in source code. Example:
```js
/**
 *
 * @type {MenuComponent}
 */
const component = ComponentFactory.get(MenuComponent);
```
