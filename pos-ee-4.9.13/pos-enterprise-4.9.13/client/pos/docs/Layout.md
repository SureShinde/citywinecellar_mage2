# Layout Mechanism

A module can add UI to screen/page via layout mechanism, it works like event.

## Customize Point
In a component, we should use layout to allow other module can add UI

For example: We use layout for Login component

```js
import SharingAccountPopup from "./SharingAccountPopup";
import layout from "../../../framework/Layout"

export class Login extends CoreComponent {
    static className = 'Login';

    template() {
        return (
            <Fragment>
            <form className="wrapper-login" onSubmit={e => e.preventDefault()}>
                <div className="form-login">
                    <strong className="logo">
                        <a href=""><img src={this.state.logoUrl} alt=""/></a>
                    </strong>

                    /* Use layout to add customize point */
                    {layout('user')('login_title_before')()(this)}

                    <h2 className="page-title">{this.props.t('Login')}</h2>
                    <div className="form-group group-username">
                        <label><span> {this.props.t('Username')}</span></label>
                        <input id="username" name="username" type="text"
                               className="form-control" placeholder={this.props.t('Username')}
                               ref="username"
                               onChange={() => this.checkActiveLogin()}
                               onKeyPress={(e) => this.handleUserKeyPress(e)}
                               autoCapitalize="none"
                        />
                    </div>
                    <div className="form-group group-password">
                        <label><span>{this.props.t('Password')}</span></label>
                        <input id="password" name="password" type="password"
                               className="form-control" placeholder={this.props.t('Password')}
                               ref="password"
                               onChange={() => this.checkActiveLogin()}
                               onClick={() => this.resetPassword()}
                               onBlur={() => this.checkResetPassword()}
                               onKeyPress={(e) => this.handlePasswordKeyPress(e)}
                               autoComplete="off"
                        />
                    </div>
                    <div className="form-group">
                        {loginButton}
                    </div>
                </div>
            </form>
            </Fragment>
        );
    }

}

```

Command `layout('user')('login_title_before')()(this)`

1. `layout('user')`: Return a layout function for `user`
2. `layout('user')('login_title_before')`: Return a layout function for `user.login_title_before`. It equals to `layout('user.login_title_before')`
3. `layout('user')('login_title_before')()`: Return a function config of layout, it will receive config from plugin (module)
4. `layout('user')('login_title_before')()(this)`: Call layout plugin with param (this)

## Plugin to add customize
In the module, we register a layout customize in file `etc/config.js`.

For example: We register a layout customize for Login
```js
import ModuleConfigAbstract from "../../ModuleConfigAbstract";

class HelloWorldConfig extends ModuleConfigAbstract{
    module = ['helloworld'];
    layout = {
        user: {
            login_title_before: [
                'STATUS: ',
                function(component) {
                    return component.state.active ? 'Active' : 'Inactive'
                },
            ]
        }
    };
}

export default (new HelloWorldConfig());
```

As above, layout plugin functions will be called with param (this). `layout('user')('login_title_before')()(this)` will return value of
```js
[
    'STATUS: ',
    function(component) {
        return component.state.active ? 'Active' : 'Inactive'
    },
]
```
that each function is called with param (this), then the result may be
```js
[
    'STATUS: ',
    'Active',
]
```

That means, layout plugin can be plain component or a function

For more case, please view files `src/framework/Layout.js` and `src/framework/__tests__/Layout-test.js`
