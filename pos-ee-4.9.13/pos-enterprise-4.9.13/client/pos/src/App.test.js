import React from 'react';
import ReactDOM from 'react-dom';
import {Provider} from 'react-redux';
import AppStore from './view/store/store';
import App from './App';
import GLOBAL_VARIABLES from './config/Config';

it('renders without crashing', () => {
  GLOBAL_VARIABLES.db = 'IndexedDb';
  const div = document.createElement('div');
  ReactDOM.render(<Provider store={AppStore}><App /></Provider>, div);
  ReactDOM.unmountComponentAtNode(div);
});
