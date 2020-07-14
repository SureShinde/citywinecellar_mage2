# Rewrite Class

A plugin can rewrite a class from `core` or `other plugin`.

## Config Rewrite
In the plugin, we register rewrite in file `etc/config.js`.

For example: We rewrite MenuComponent
```js
import rewriteMenu from "../view/menu";
import ModuleConfigAbstract from "../../ModuleConfigAbstract";

class HelloWorldConfig extends ModuleConfigAbstract{
    module = ['helloworld'];
    rewrite = {
        service: {
            // "UserService": BlaService
        },
        container: {
            // "LoginContainer": HelloWorldContainer
        },
        component: {
            MenuComponent: rewriteMenu
        },
    };
}

export default (new HelloWorldConfig());
```

We have 7 type of class can be rewrite:

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

## Rewrite Implementation
To implement rewrite, we need using javascript function. For example, we implement rewrite for MenuComponent as:

```js
import React, {Fragment} from "react";

/**
 * Rewrite MenuComponent
 *
 * @param {MenuComponent} MenuComponent
 * @returns {RewriteClass}
 */
export default function(MenuComponent) {
    return class Rewrite extends MenuComponent {
        template() {
            let template = super.template();
            return (
              <Fragment>
                Hello World!
                {template}
              </Fragment>
            )
        }
    }
}
```
