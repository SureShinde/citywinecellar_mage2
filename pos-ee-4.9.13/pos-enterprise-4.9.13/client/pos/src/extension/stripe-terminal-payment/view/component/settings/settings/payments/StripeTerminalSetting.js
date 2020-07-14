import React, {Fragment} from "react";
import {Panel, PanelGroup} from "react-bootstrap";
import ComponentFactory from '../../../../../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../../../../../framework/container/CoreContainer';
import ContainerFactory from "../../../../../../../framework/factory/ContainerFactory";
import AbstractGrid from "../../../../../../../framework/component/grid/AbstractGrid";
import SmoothScrollbar from "smooth-scrollbar";
import StripeTerminalConstant from "../../../../constant/payment/StripeTerminalConstant";
import RegisterNewTerminal from "./stripe-terminal-setting/RegisterNewTerminal";
import ConnectTerminal from "./stripe-terminal-setting/ConnectTerminal";

export class StripeTerminalSetting extends AbstractGrid {
    static className = 'StripeTerminalSetting';
    panels = [];

    /**
     *
     * @param element
     * @return {*}
     */
    setBlockListElement = element => this.list = element;

    /**
     * @param props
     */
    constructor(props) {
        super(props);
        this.setupData();
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
     *  prepare dât, allow plugin
     */
    setupData() {
        this.panels = [ConnectTerminal,RegisterNewTerminal];
        this.state = {
            activeKey: ConnectTerminal.className
        };
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
     *
     * @param activeKey
     */
    handleSelectPanelHeader(activeKey) {
        this.setState({activeKey});
    };

    /**
     * template
     * @returns {*}
     */
    template() {
        const {t} = this.props;
        return (
            <Fragment>
                <div className="settings-right">
                    <div className="block-title">
                        <button className="btn-back" onClick={() => this.closeOwn()}>
                            <span>back</span>
                        </button>
                        <strong className="title">{t(StripeTerminalConstant.TITLE)}</strong>
                    </div>
                    <div className="block-content" ref={this.setBlockListElement}>
                        <PanelGroup
                            accordion
                            id={`accordion-${StripeTerminalConstant.CODE}`}
                            className={`accordion-${StripeTerminalConstant.CODE}`}
                            activeKey={this.state.activeKey}
                            onSelect={activeKey => this.handleSelectPanelHeader(activeKey)}
                        >
                            {this.panels.map(Content => {
                                let className = Content.className;
                                return (
                                    <Panel eventKey={className} key={className}>
                                        <Panel.Heading>
                                            <Panel.Title toggle>{t(Content.title)}</Panel.Title>
                                        </Panel.Heading>
                                        <Panel.Body collapsible>
                                            <Content/>
                                        </Panel.Body>
                                    </Panel>
                                )
                            })}
                        </PanelGroup>
                    </div>
                </div>
            </Fragment>
        )
    }
}

class StripeTerminalSettingContainer extends CoreContainer {
    static className = 'StripeTerminalSettingContainer ';
}

/**
 * @type {StripeTerminalSettingContainer}
 */
export default ContainerFactory.get(StripeTerminalSettingContainer).withRouter(
    ComponentFactory.get(StripeTerminalSetting)
);