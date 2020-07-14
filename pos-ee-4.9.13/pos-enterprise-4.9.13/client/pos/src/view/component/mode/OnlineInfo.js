import React, { Fragment } from 'react';
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import CoreContainer from "../../../framework/container/CoreContainer";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import {CoreComponent} from "../../../framework/component";
import SyncConstant from '../../constant/SyncConstant';
import '../../style/css/Mode.css';

export class OnlineInfo extends CoreComponent {
    static className = 'OnlineInfo';

    setPopover = element => this.popover = element;

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            showPopover: false,
            popoverLeft: 0,
        };
    }

    showPopover(event) {
        this.setState({
            showPopover: true,
            popoverLeft: event.target.getBoundingClientRect().left - 135,
        });
        this.popoverModal.style.display = "block";
    }

    componentDidMount() {
        this.addPopoverModal();
    }

    hidePopover() {
        this.setState({showPopover: false});
        this.popoverModal.style.display = "none";
    }

    addPopoverModal() {
        this.popoverModal = document.createElement("div");
        this.popoverModal.className = "modal-backdrop fade in popover-backdrop popover-backdrop_option";
        this.popoverModal.style.position = "absolute";
        this.popoverModal.style.display = "none";
        this.popoverModal.onclick = () => this.hidePopover();
        document.body.appendChild(this.popoverModal);
        document.body.appendChild(this.popover);
    }

    /**
     * template
     * @returns {*}
     */
    template() {
        let isOffline = this.props.mode === SyncConstant.OFFLINE_MODE
        let message = this.props.t(
            "Online mode is active. Products won’t display quantity and final price."
        )
        return (
            <Fragment>
                <div className="online-info" onClick={(event) => this.showPopover(event)} title={message} style={{
                    display: isOffline ? 'none' : 'block',
                }}></div>
                <div ref={this.setPopover}
                    className="popover fade bottom in" role="tooltip"
                    style={{
                        'top': '56px',
                        'left': this.state.popoverLeft + 'px',
                        'width': '300px',
                        'display': this.state.showPopover ? 'block' : 'none'
                    }}
                >
                    <div className="arrow"></div>
                    <div className="popover-content" style={{
                        'marginTop': '-2px',
                        'border': '1px solid rgba(0,0,0,.25)',
                        'padding': '10px',
                    }}>{message}</div>
                </div>
            </Fragment>
        );
    }
}

export class OnlineInfoContainer extends CoreContainer {
    static className = 'OnlineInfoContainer';

    /**
     * map state to props
     * @param state
     * @returns {{count: *, total: *}}
     */
    static mapState(state) {
        let {mode} = state.core.sync;
        return {
            mode
        }
    }
}
export default ContainerFactory.get(OnlineInfoContainer).withRouter(
    ComponentFactory.get(OnlineInfo)
)
