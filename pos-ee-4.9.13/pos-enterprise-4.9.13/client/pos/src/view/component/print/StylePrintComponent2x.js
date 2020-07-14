import React from 'react';
import CoreComponent from "../../../framework/component/CoreComponent";

export const BARCODE_HEIGHT = 70;
export const BARCODE_WIDTH = 2.6;
export const BARCODE_FONT_SIZE = 24;

export default class StylePrintComponent2x extends CoreComponent {
    static className = 'StylePrintComponent2x';

    /**
     *  component render DOM expression
     *  @return string
     *
     * */
    template() {
        return (
            <style jsx="true">{`
                        body { min-width: 600px !important }
                        .block-printreceipt {
                        margin: 0 auto;
                        padding: 50px 30px;
                        background-color: #fff;
                        text-align: center;
                        color: #000000;
                        font-size: 22px;
                        font-family: 'Helvetica';
                    }
                        .block-printreceipt p {
                        margin-bottom: 6px;
                        margin-top: 0;
                    }

                        .block-printreceipt hr {
                        border-color: #000000;
                        border-width: 2px 0 0 ;
                        border-style: dashed;
                        margin: 4px 0;
                    }

                        .block-printreceipt table {
                        width: calc(100% - 30px);
                        text-align: left;
                        line-height: 40px;
                    }

                        .block-printreceipt table tr td,
                        .block-printreceipt table tr th  {
                        vertical-align: text-top;
                        padding: 4px 0px;
                        border: none;
                        line-height: 32px;
                    }

                        .block-printreceipt table tr .t-qty,
                        .block-printreceipt table tr .t-price {
                    }

                        .block-printreceipt .t-name {
                        max-width: 240px;
                    }
                        .block-printreceip .t-refund-label {
                            max-width: 80px;
                        }

                        .block-printreceipt .t-qty,
                        .block-printreceipt .t-price,
                        .block-printreceipt .t-total {
                        white-space: nowrap;
                    }

                        .block-printreceipt .t-bundle {
                        padding-left: 20px;
                    }

                        .block-printreceipt i {
                        font-size: 20px;
                    }

                        .block-printreceipt .title {
                        font-size: 50px;
                        display: block;
                    }

                        .block-printreceipt .text-right {
                        text-align: right;
                    }

                        .block-printreceipt .text-center {
                        text-align: center;
                    }

                        .block-printreceipt .text-left {
                        text-align: left;
                    }

                    table {
                        font-size: 22px;
                    }


                    .block-printreceipt .reprint {
                        letter-spacing: 2.8px;
                        font-size: 24px;
                        font-weight: normal;
                        color: #000000;
                        line-height: 32px;
                        padding-top: 32px;
                    }

                    .block-printreceipt .reprint span {
                        display: inline-block;
                        vertical-align: middle;
                    }

                    .block-printreceipt .reprint strong {
                        letter-spacing: 0;
                        color: #000000;
                        padding: 0 6px;
                        display: inline-block;
                        vertical-align: middle;
                        font-weight: normal;
                    }

                    .hidden{display:none!important}

                    pre {
                        background-color: #fff;
                        border: none;
                        width: 500px;
                        margin: auto;
                    }

                    `}</style>
        );
    }
}
