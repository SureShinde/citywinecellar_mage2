import React, {Fragment} from "react";
import ComponentFactory from '../../../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../../../framework/container/CoreContainer';
import ContainerFactory from "../../../../../framework/factory/ContainerFactory";
import SmoothScrollbar from "smooth-scrollbar";
import AbstractGrid from "../../../../../framework/component/grid/AbstractGrid";
import StarWebPrintService from "../../../../../service/printer/StarWebPrintService";
import {toast} from "react-toastify";
import '../../../../style/css/Setting.css';


export class StarWebPrint extends AbstractGrid {
    static className = 'StarWebPrint';
    static title = 'Star WebPRNT Printer';

    setBlockListElement = element => this.list = element;

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            currentItem: "",
            isRequesting: false,
        }
    }

    /**
     * Init smooth scrollbar
     */
    componentDidMount() {
        if (!this.scrollbar && this.list) {
            this.scrollbar = SmoothScrollbar.init(this.list);
        }
    }

    /**
     * destroy scrollbar and call closeChild() PaymentList
     */
    closeOwn() {
        this.props.closeChild();
        SmoothScrollbar.destroy(this.list);
        this.scrollbar = null;
    }

    /**
     * Save IP
     *
     * @param event
     */
    saveHost(event) {
        StarWebPrintService.setHost(event.target.value);
    }

    /**
     * Save Port
     *
     * @param event
     */
    savePort(event) {
        StarWebPrintService.setPort(event.target.value);
    }


    /**
     * test payment
     */
    test() {
        if (this.state.isRequesting) {
            return;
        }
        this.setState({isRequesting: true});
        StarWebPrintService.test(() => {
            this.connectionSuccess();
        }, (reason) => {
            this.connectionFail(reason);
        });
    }


    /**
     * Connect  success
     */
    connectionSuccess() {
        toast.success(
            this.props.t("Printer is connected successfully!"),
            {
                position: toast.POSITION.TOP_CENTER,
                className: 'wrapper-messages messages-success',
                autoClose: 1000
            }
        );
        this.setState({isRequesting: false})
    }


    /**
     * Connect  fail
     */
    connectionFail(reason) {
        toast.error(
            this.props.t(reason),
            {
                className: 'wrapper-messages messages-warning',
                autoClose: 1000
            }
        );
        this.setState({isRequesting: false})
    }

    /**
     * Destroy smooth scrollbar
     */
    componentWillUnmount() {
        SmoothScrollbar.destroy(this.list);
        this.scrollbar = null;
    }

    /**
     * template
     * @returns {*}
     */
    template() {

        const {isRequesting} = this.state;

        return (
            <Fragment>
                <div className="settings-right">
                    <div className="block-title">
                        <button className="btn-back" onClick={() => this.closeOwn()}>
                            <span>back</span>
                        </button>
                        <strong className="title">{this.props.t(StarWebPrint.title)}</strong>
                    </div>
                    <div className="block-content" ref={this.setBlockListElement}>
                        <ul className="list-lv1">
                            <li>
                                <span className="title">
                                    {this.props.t('Enable')}
                                    <br></br>
                                    <span className="title-description">
                                        {this.props.t('Guide to configure WebPRNT')}
                                        <a rel="noopener noreferrer" target="_blank" href="https://drive.google.com/open?id=1XaihfKeNAw3wDLq68f4tqklWBpHcO6VfCWhx4_vqD24">HTTP</a>
                                        or <a rel="noopener noreferrer" target="_blank" href="https://drive.google.com/open?id=135c1YJYUvhYOx2nobcyNz7n4KVvifxJXzLnQ_C9K0kc">HTTPS</a>
                                    </span>
                                </span>
                                <span className="value">
                                    <label className="checkbox">
                                        <input type="checkbox"
                                               defaultChecked={StarWebPrintService.isEnable()}
                                               onChange={(event) =>
                                                   StarWebPrintService.toggleEnable(event.target.checked)}
                                        />
                                        <span></span>
                                    </label>
                                </span>
                            </li>
                            <li>
                                <label className="title">{this.props.t('Host')}</label>
                                <input type="text"
                                       className="form-control"
                                       defaultValue={StarWebPrintService.getHost()}
                                       onChange={(event) => this.saveHost(event)}
                                />
                            </li>
                            <li>
                                <label className="title">{this.props.t('Port')}</label>
                                <input type="text"
                                       className="form-control"
                                       defaultValue={StarWebPrintService.getPort()}
                                       onChange={(event) => this.savePort(event)}
                                />
                            </li>
                        </ul>
                    </div>
                    <div className="block-bottom">
                        <button
                            className="btn btn-default"
                            type="button"
                            onClick={() => this.test()}>
                            {
                                isRequesting ? (<div className="loader-setting"/>) :this.props.t('Check')
                            }
                        </button>
                    </div>
                </div>
            </Fragment>
        )
    }
}

class StarWebPrintContainer extends CoreContainer {
    static className = 'StarWebPrintContainer';
}

/**
 * @type {StarWebPrintContainer}
 */
export default ContainerFactory.get(StarWebPrintContainer).withRouter(
    ComponentFactory.get(StarWebPrint)
);