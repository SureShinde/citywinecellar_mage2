import React, {Fragment} from 'react';
import {Modal} from 'react-bootstrap'
import CoreComponent from "../../../../framework/component/CoreComponent";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";
import CoreContainer from "../../../../framework/container/CoreContainer";
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import QuoteAction from "../../../action/checkout/QuoteAction";
import "../../../style/css/AddComment.css";


export class AddCommentComponent extends CoreComponent {
    static className = 'AddCommentComponent';

    setCommentElement = element => this.commentElement = element;

    /**
     *
     */
    constructor() {
        super();
        this.state = {
            isOpen: false
        }
    }


    /**
     * check delete cart or not
     *
     * @param quote
     * @returns {boolean}
     */
    canAddComment(quote) {
        return quote && quote.id;
    }

    /**
     * Hide popup
     *
     * @returns {*}
     */
    toggle() {
        this.setState(prevState => {
            return {isOpen: !prevState.isOpen};
        });
    }

    /**
     * Cart button handle
     */
    showAddCommentModal() {
        this.toggle();
    }

    /**
     * Remove cart
     */
    cancelAddComment() {
        this.toggle();
    }

    /**
     * Remove cart
     */
    saveComment() {
        if (this.commentElement && this.commentElement.hasOwnProperty('value')) {
            let quote = this.props.quote;
            quote.comment = this.commentElement.value;
            this.props.actions.setQuote(quote);
            this.toggle();
        }
    }

    /**
     *
     * @returns {string}
     */
    getDefaultComment() {
        let quote = this.props.quote;
        return quote && quote.comment ? quote.comment : "";
    }

    /**
     * Render template
     *
     * @returns {*}
     */
    template() {
        let buttonClass = 'btn btn-add-comment';
        let isDisabled = false;
        if (!this.canAddComment(this.props.quote)) {
            buttonClass += ' disabled';
            isDisabled = true;
        }
        return (
            <Fragment>
                <button className={buttonClass} disabled={isDisabled} type="button"
                        onClick={() => this.showAddCommentModal()}
                >
                    <span>comment</span>
                </button>
                <Modal
                    bsSize={"lg"}
                    className={"popup-messages popup-add-comment"}
                    show={this.state.isOpen}
                    onHide={ this.toggle.bind(this) }
                >
                    <div className="modal-header">
                        <button type="button" className="cancel" data-dismiss="modal" aria-label="Close"
                                onClick={() => this.cancelAddComment()}>
                            {this.props.t('Cancel')}
                        </button>
                        <h4 className="modal-title">{this.props.t('Add Comment')}</h4>
                        <button type="button" className="save" onClick={() => this.saveComment()}>
                            {this.props.t('Save')}
                        </button>
                    </div>
                    <div className="modal-body">
                        <div className="add-comment-order">
                            <div className="box-text-area">
                                <textarea ref={this.setCommentElement}
                                          className="form-control"
                                          placeholder={this.props.t('Add comment for this order')}
                                          defaultValue={this.getDefaultComment()}
                                          style={{resize: 'none'}}>
                                </textarea>
                            </div>
                        </div>
                    </div>
                </Modal>
            </Fragment>
        );
    }
}

export class AddCommentContainer extends CoreContainer {
    static className = 'AddCommentContainer';

    static mapDispatch(dispatch) {
        return {
            actions: {
                setQuote: quote => dispatch(QuoteAction.setQuote(quote))
            }
        }
    }

    /**
     *
     * @param state
     * @return {{quote: *}}
     */
    static mapState(state) {
        const {quote} = state.core.checkout;
        return {quote}
    }
}

/**
 *
 * @type {AddCommentComponent}
 */
export default ContainerFactory.get(AddCommentContainer).getConnect(ComponentFactory.get(AddCommentComponent))
