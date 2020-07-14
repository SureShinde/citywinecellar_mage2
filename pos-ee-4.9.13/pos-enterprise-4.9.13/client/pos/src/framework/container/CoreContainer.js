import {connect} from "react-redux";
import {withRouter} from "react-router-dom";
// import AppStore from "../../view/store/store";

export default class CoreContainer {
  static className = 'CoreContainer';
  component;

    /**
     * constructor method
     *
     * @param component
     */
  constructor(component) {
    this.component = component
  }

  /**
   *  abstract method
   *
   * @param state
   * @return {{}}
   */
  static mapState(state) {
    return {}
  }

  /**
   * abstract method
   *
   * @param dispatch
   * @return {{}}
   */
  static mapDispatch(dispatch) {
    return {}
  }

    /**
     *  combine mapStateToProps
     *
     * @return {function(*=)}
     */
  static mapStateToProps() {
    return (state) => {
      let payload = {
        mapStateToProps: {}
      };

      // AppStore.dispatch({
      //   type: `Before${this.className}MapStateToProps`,
      //   payload
      // });

      return {
        ...payload.mapStateToProps,
        ...this.mapState(state),
      }
    }
  }

    /**
     * combine mapDispatchToProps
     *
     * @return {function(*=)}
     */
  static mapDispatchToProps() {
    return (dispatch) => {
      let payload = {
        dispatch,
        mapDispatchToProps: {}
      };

      // AppStore.dispatch({
      //   type: `Before${this.className}MapDispatchToProps`,
      //   payload
      // });

      return {
        ...payload.mapDispatchToProps,
        ...this.mapDispatch(dispatch),
      }
    }
  }

    /**
     *
     * get redux connection from container
     *
     * @param component
     * @param mapState
     * @param mapDispatch
     * @return {*}
     */
  static getConnect(
    component,
    mapState = this.mapStateToProps(),
    mapDispatch = this.mapDispatchToProps()
  ) {
    return connect(mapState, mapDispatch)(component)
  }

    /**
     * bind router wrapper to redux connection from container
     *
     * @param component
     * @param mapState
     * @param mapDispatch
     * @return {*}
     */
  static withRouter(
    component,
    mapState = this.mapStateToProps(),
    mapDispatch = this.mapDispatchToProps()
  ) {
    return withRouter(this.getConnect(
      component,
      mapState,
      mapDispatch
    ))
  }
}

