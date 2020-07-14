import React, {Fragment} from "react";
import ComponentFactory from '../../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../../framework/container/CoreContainer';
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import AbstractGrid from "../../../../framework/component/grid/AbstractGrid";
import SmoothScrollbar from "smooth-scrollbar";
import StarWebPrint from "./printers/StarWebPrint";

export class Printers extends AbstractGrid {
    static className = 'Printers';
    setBlockListElement = element => this.list = element;

    /**
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            openChild: false,
            currentChild: ""
        }
    }

    items = [
        {
            "id": StarWebPrint.className,
            "title": StarWebPrint.title,
            "name": StarWebPrint.title,
            "component": StarWebPrint,
            "visible": true
        },

    ];

    /**
     * open child detail of payment setting
     * @param childName
     */
    openChild(childName) {
        this.setState({
            openChild: true,
            currentChild: childName
        });
    }

    /**
     * close child back
     */
    closeChild() {
        this.setState({
            openChild: false,
            currentChild: ""
        });
    }

    /**
     * Init smooth scrollbar
     */
    componentDidMount() {
        if (!this.scrollbar && this.list) {
            this.scrollbar = SmoothScrollbar.init(this.list)
        }
    }

    /**
     * Destroy smooth scrollbar when unmount component
     */
    componentWillUnmount() {
        SmoothScrollbar.destroy(this.list);
        this.scrollbar = null;
    }

    /**
     * Destroy smooth scrollbar and create scroll bar
     */
    componentDidUpdate() {
        SmoothScrollbar.destroy(this.list);
        this.scrollbar = null;
        if (!this.scrollbar && this.list) {
            this.scrollbar = SmoothScrollbar.init(this.list)
        }
    }

    /**
     * template
     * @returns {*}
     */
    template() {
        let parents = (
            <div className="settings-right">
                <div className="block-title">
                    <strong className="title"></strong>
                </div>
                <div className="block-content" ref={this.setBlockListElement}>
                    <ul className="list-lv0">
                        {
                            this.items.map(item => {
                                return (
                                    item.visible === false ?
                                        "" :
                                        <li key={item.id}
                                            onClick={() => this.openChild(item.title)}>
                                            <a>{item.name}</a>
                                        </li>
                                )
                            })
                        }
                    </ul>
                </div>
            </div>
        );
        return (
            <Fragment>
                {this.state.openChild ? "" : parents}
                {
                    this.items.map(item => {
                        let Element = item.component;
                        return (this.state.currentChild === item.title) &&
                            (Element !== "") &&
                            <Element key={item.id} closeChild={() => this.closeChild()}/>
                    })
                }

            </Fragment>
        )
    }
}

class PrintersContainer extends CoreContainer {
    static className = 'PrintersContainer';
}

/**
 * @type {PrintersContainer}
 */
export default ContainerFactory.get(PrintersContainer).withRouter(
    ComponentFactory.get(Printers)
);