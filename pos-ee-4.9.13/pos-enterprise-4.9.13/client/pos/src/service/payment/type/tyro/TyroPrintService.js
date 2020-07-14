import CoreService from "../../../CoreService";
import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import PrinterService from "../../../PrinterService";

export class TyroPrintService extends CoreService {
    static className = 'TyroPrintService';

    /**
     * Print tyro receipt
     *
     * @param receiptData
     * @return {boolean}
     */
    print(receiptData) {
        if (!receiptData) {
            return false;
        }

        PrinterService.print(`
                <html>
                <style>
                    @media screen {.paper {margin: 5px auto; max-width: 250px;}} 
                    @media print {
                        
                    }
                    @page{ size: auto; margin: 0 4mm;}
                   
                   body {
                            max-width: 100%;
                            max-height: 100%;
                            color: #000; background-color: #fff;
                   }
                    .page {
                       width: 250px;
                       margin: 0 auto;
                    }
                    
                    pre {
                        display: block;
                        padding: 9.5px;
                        margin: 0 0 10px;
                        font-size: 13px;
                        line-height: 20px;
                        word-break: break-all;
                        word-wrap: break-word;
                        white-space: pre;
                        white-space: pre-wrap;
                    }
                </style>
                <body>
                    <div class="page">
                        <pre>${receiptData}</pre>
                    </div>
                </body>
                </html>
            `,
            'print_offline', 'status=1,width=500,height=700'
            );
    }
}

/** @type TyroPrintService */
let tyroPrintService = ServiceFactory.get(TyroPrintService);

export default tyroPrintService;