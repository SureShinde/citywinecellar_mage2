import React, {Fragment} from 'react';
import '../../style/css/Category.css';
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../framework/container/CoreContainer";
import CategoryAction from "../../action/CategoryAction";
import Config from "../../../config/Config";
import AbstractList from "../../../framework/component/list/AbstractList";
import {Modal} from "react-bootstrap";
import SmoothScrollbar from "smooth-scrollbar";
import SyncConstant from "../../constant/SyncConstant";

export class CategoryList extends AbstractList {
    static className = 'CategoryList';

    category_list = null;

    setCategoryListElement = element => {
        this.category_list = element;
        if (!this.scrollbar) {
            this.scrollbar = SmoothScrollbar.init(this.category_list);
        }
    };

    /**
     * Constructor
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            title: this.props.t('All products'),
            titleWrapper: this.props.t('All products'),
            isOpenPopup: false,
            category_id: null,
            saveParentId: Config.config.root_category_id,
            parent_id: Config.config.root_category_id,
            parentCategory: null,
            items: []
        }
    }

    /**
     * componentWillMount
     */
    componentWillMount() {
        /* Set default state mode for component from Config */
        if (Config.dataTypeMode && Config.dataTypeMode[SyncConstant.TYPE_CATEGORY]) {
            this.setState({mode: Config.dataTypeMode[SyncConstant.TYPE_CATEGORY]});
        }
    }

    /**
     * This function after mapStateToProps then push more items to component or change load categories mode
     *
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        if (nextProps.searchKey) {
            if (nextProps.category_id === null) {
                this.setState({
                    category_id: null,
                });
            }
        }
        if (!this.isModeChange(nextProps)) {
            if (
                nextProps.parent_id === this.state.parent_id
                && nextProps.dataTypeMode
                && this.state.mode === nextProps.dataTypeMode[SyncConstant.TYPE_CATEGORY]
                && nextProps.requestMode === this.state.mode
            ) {
                this.addItems(nextProps.categories);
                this.setState({
                    parentCategory: nextProps.parentCategory,
                    title: this.splitCategoryTitle(nextProps.parentCategory.name),
                });
                this.stopLoading();
                if (this.scrollbar) {
                    this.scrollbar.scrollTo(0, 0);
                }
            }
        }
    }

    /**
     * Check mode is changed and reload product list
     *
     * @param nextProps
     * @return {boolean}
     */
    isModeChange(nextProps) {
        if (
            nextProps.dataTypeMode
            && nextProps.dataTypeMode[SyncConstant.TYPE_CATEGORY]
            && (nextProps.dataTypeMode[SyncConstant.TYPE_CATEGORY] !== this.state.mode)
        ) {
            this.setState({mode: nextProps.dataTypeMode[SyncConstant.TYPE_CATEGORY]});
            this.startLoading();
            this.clearItems();
            this.loadCategory(this.state.parent_id);
            return true;
        }
        return false;
    }

    /**
     * load categories
     * @param parentId
     */
    loadCategory(parentId) {
        this.props.actions.getListCategory(parentId);
    }

    /**
     * This function update category when click 1 category
     * @param id
     */
    updateCategory(category) {
        if (category) {
            this.setState({
                category_id: category.id,
                saveParentId: this.state.parent_id,
            });
        } else {
            this.setState({
                category_id: null,
                saveParentId: Config.config.root_category_id,
                parent_id: Config.config.root_category_id,
            });
        }
    }

    /**
     * This function change id category turn into idCategory, product will show
     * @param category
     */
    showProduct(category) {
        if (category) {
            this.props.changeCategory(Number(category.id));
            this.updateCategory(category);
            this.setState({
                isOpenPopup: false,
                titleWrapper: this.splitCategoryTitle(category.name),
            });
        } else {
            this.props.changeCategory();
            this.updateCategory(null);
        }
    }

    /**
     * This function will change title fit with <div> tag category
     * @param title
     * @returns {*}
     */
    splitCategoryTitle(title) {

        if (!title) {
            return '';
        }

        let widthParentEl = 14;
        let widthElement = title.length;
        let categoryTitle = title;
        while (widthParentEl < widthElement) {
            categoryTitle = categoryTitle.split(' ');
            if (categoryTitle.length === 1)
                break;
            categoryTitle.pop();
            categoryTitle = categoryTitle.join(' ');
            widthElement = categoryTitle.length;
        }
        return categoryTitle;
    }

    /**
     * This function will change name fit with <div> tag category
     * @param name
     * @returns {*}
     */
    splitCategoryName(name) {
        let widthParentEl = 30;
        let widthElement = name.length;
        let categoryName = name;
        while (widthParentEl < widthElement) {
            categoryName = categoryName.split(' ');
            if (categoryName.length === 1)
                break;
            categoryName.pop();
            categoryName = categoryName.join(' ');
            widthElement = categoryName.length;
        }
        return categoryName;
    }

    /**
     * Show category popup
     */
    showPopup() {
        if (this.state.isOpenPopup === false) {
            this.setState({
                isOpenPopup: true,
                parent_id: this.state.saveParentId
            });
            this.loadCategory(this.state.saveParentId);
            this.startLoading();
        } else {
            this.setState({
                isOpenPopup: false
            });
        }
    }

    /**
     * This function reset initial state
     */
    reset() {
        this.setState({
                items: [],
                category_id: Config.config.root_category_id,
                parent_id: Config.config.root_category_id,
                saveParentId: Config.config.root_category_id,
                title: this.splitCategoryTitle(this.props.t('All products')),
                titleWrapper: this.splitCategoryTitle(this.props.t('All products')),
                isOpenPopup: false
            },
            () => this.showProduct()
        );
    }

    /**
     * This function update state into parents when click className = "dl-back" button
     */
    back() {
        if (!this.state.parentCategory) {
            return null;
        }
        this.setState({
            parent_id: this.state.parentCategory.parent_id,
        });
        this.loadCategory(this.state.parentCategory.parent_id);
        this.startLoading();
    }

    /**
     * This function open child category when click className="toggle-submenu" button
     * @param id
     */
    openChild(category) {
        this.setState({
            parent_id: category.id,
            parentCategory: category,
            title: this.splitCategoryTitle(category.name),
        });
        this.loadCategory(category.id);
        this.startLoading();
    }

    /**
     * Render template
     *
     * @return {*}
     */
    template() {
        return (
            <Fragment>
                <div className="category-product-container">
                    <div
                        className={this.state.isOpenPopup ? "dropdown-toggle category-results dl-trigger dl-active" : "dropdown-toggle category-results dl-trigger"}
                        data-toggle="modal" data-target="#popup-drop-category"
                        onClick={() => this.showPopup()}
                    >
                        <span className="text">{this.state.titleWrapper}</span>
                    </div>
                </div>
                <Modal
                    bsSize={"sm"}
                    className={this.state.isOpenPopup ? "popup-drop-category" : "popup-drop-category hidden"}
                    backdropClassName={this.state.isOpenPopup ? "" : "hidden"}
                    dialogClassName={this.state.isOpenPopup ? "" : "hidden"}
                    show={true}
                    onHide={() => this.setState({isOpenPopup: false})}
                >
                    <div className="category-drop">
                        <div className="category-top" onClick={() => this.reset()}>
                            <a>{this.props.t('All products')}</a>
                        </div>
                    </div>
                    <div id="dl-menu" className="dl-menuwrapper" >
                        <div className={"dl-menu dl-menu-toggle dl-menuopen"} ref={this.setCategoryListElement}>
                            <ul
                                tabIndex={"1"}
                                style={{overflow: "hidden", outline: "none"}}>

                                {(!this.state.parent_id ||
                                    Number(this.state.parent_id) === Number(Config.config.root_category_id)) ?
                                    (
                                        <li className="menu-label">
                                            <a style={{padding: 15}}>{this.props.t('Select Category')}</a>
                                        </li>) :
                                    (<li className="dl-back"><a onClick={() => this.back()}>{this.state.title}</a></li>)
                                }
                                {
                                    this.state.items.length ?
                                        this.state.items.map((category) => {
                                            return (
                                                <li className={"dl-subview"}
                                                    key={category.id}>
                                                    <a
                                                        onClick={() => this.showProduct(category)}
                                                        style={
                                                            (category.id === this.state.category_id) ?
                                                                {color: "#007aff"}
                                                                : {}
                                                        }
                                                    >{this.splitCategoryName(category.name)}</a>
                                                    {
                                                        (category.children_ids &&  category.children_ids.length) ?
                                                            (
                                                                <span className="toggle-submenu"
                                                                      onClick={() => this.openChild(category)}>
                                                                    <span>open submenu</span>
                                                                </span>
                                                            )
                                                            : null
                                                    }
                                                </li>
                                            )
                                        })
                                        : null
                                }
                                <div className="loader-couponcode"
                                     style={{display: (this.isLoading() ? 'block' : 'none')}}>
                                    <div className="loader-product"/>
                                </div>
                            </ul>
                        </div>
                    </div>
                </Modal>
            </Fragment>
        );
    }
}

class CategoryListContainer extends CoreContainer {
    static className = 'CategoryListContainer';

    // This maps the state to the property of the component
    static mapState(state) {
        let {mode, dataTypeMode} = state.core.sync;
        let {parentCategory, categories, search_criteria, total_count, parent_id, requestMode} = state.core.category;
        return {
            mode, dataTypeMode, parentCategory, categories, search_criteria, total_count, parent_id, requestMode
        };
    }

    // This maps the dispatch to the property of the component
    static mapDispatch(dispatch) {
        return {
            actions: {
                getListCategory: (parent_id = null) =>
                    dispatch(CategoryAction.getListCategory(parent_id))
            }
        }
    }
}

export default ContainerFactory.get(CategoryListContainer).withRouter(
    ComponentFactory.get(CategoryList)
)

