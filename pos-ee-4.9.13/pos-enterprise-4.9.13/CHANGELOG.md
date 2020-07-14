v4.9.13
=============

#### Bug Fixed
1. [#1355](https://github.com/Magestore/pos-enterprise/issues/1355): [4] Missing Reward Point when create orders in backend
2. [#1349](https://github.com/Magestore/pos-enterprise/issues/1349): [4] Barcode is not generated automatically.
3. [#1348](https://github.com/Magestore/pos-enterprise/issues/1348): [4] Selection is discarded when switching filter
4. [#1347](https://github.com/Magestore/pos-enterprise/issues/1347): [4] Showing duplicate bar code when printing picking list Fullfilment
5. [#1344](https://github.com/Magestore/pos-enterprise/issues/1344): [4] MSI - Create Order from backend and ship from POS
6. [#1310](https://github.com/Magestore/pos-enterprise/issues/1310): [4] Get wrong is_in_stock when qty is greater than 0

#### Developer Issue
1. [#1304](https://github.com/Magestore/pos-enterprise/issues/1304): [4] Measuring stability figure of product

v4.9.12
=============

#### Bug Fixed
1. [#1332](https://github.com/Magestore/pos-enterprise/issues/1332): [4] Redirect to frontend when clicking on the [Generate] button
2. [#1327](https://github.com/Magestore/pos-enterprise/issues/1327): [4] Can not print Z-report in the Backend
3. [#1321](https://github.com/Magestore/pos-enterprise/issues/1321): [4] Can not save default billing & shipping to the server when adding default billing & shipping address
4. [#1319](https://github.com/Magestore/pos-enterprise/issues/1319): [4] Get wrong total after creating a PO, then delete an item from PO and save it again

v4.9.11
=============

#### Bug Fixed
1. [#1307](https://github.com/Magestore/pos-enterprise/issues/1307): [4] Can not open cash drawer automatically when printing receipt
2. [#1300](https://github.com/Magestore/pos-enterprise/issues/1300): [4] Don't sync refund to Backend

v4.9.10
=============

#### Bug Fixed
1. [#1292](https://github.com/Magestore/pos-enterprise/issues/1292): [4] Saving wrong data to the sales_order_item table
2. [#1290](https://github.com/Magestore/pos-enterprise/issues/1290): [4] Bug about pagination in the [Stock by Warehouse Report] page
3. [#1284](https://github.com/Magestore/pos-enterprise/issues/1284): [4] Could NOT cancel order created on POS
4. [#1279](https://github.com/Magestore/pos-enterprise/issues/1279): [4] Payments of order created on POS have been disappeared randomly
5. [#1267](https://github.com/Magestore/pos-enterprise/issues/1267): [4] Refund items list becomes empty after the place order request has been successfully synchronized to the server
6. [#1259](https://github.com/Magestore/pos-enterprise/issues/1259): [4] Can not cancel order, which has simple product + config/group product
7. [#1257](https://github.com/Magestore/pos-enterprise/issues/1257): [4] Compatible with magento 2.3.4
8. [#1256](https://github.com/Magestore/pos-enterprise/issues/1256): [4] Don't subtract the qty immediately after placing out order in offline mode
9. [#1254](https://github.com/Magestore/pos-enterprise/issues/1254): [4] Can not apply reward points for the bundle products
10. [#1249](https://github.com/Magestore/pos-enterprise/issues/1249): [4] out of stock product still able to sold if it's a child of configurable product
11. [#1248](https://github.com/Magestore/pos-enterprise/issues/1248): [4] Not showing the [Return to Stock] feature on the [Refund Items] screen

v4.9.9
=============

#### Bug Fixed
1. [#1199](https://github.com/Magestore/pos-enterprise/issues/1199): [4] Orders created on POS are lost randomly

v4.9.8
=============

#### Bug Fixed
1. [#1244](https://github.com/Magestore/pos-enterprise/issues/1244): [4] Cannot use the maximum amount of gift card code in POS
2. [#1227](https://github.com/Magestore/pos-enterprise/issues/1227): [4] Fail to load API definition
3. [#1225](https://github.com/Magestore/pos-enterprise/issues/1225): [4] [Bug] Calculate incorrect the maximum reward point apply for cart when having the custom discount
4. [#1221](https://github.com/Magestore/pos-enterprise/issues/1221): [4] Cannot login to POS after "Force Sign-out" in the backend
5. [#1163](https://github.com/Magestore/pos-enterprise/issues/1163): [4] Don't update visible on pos in table webpos_search_product if use update attributes action feature

#### Developer Issue
1. [#1234](https://github.com/Magestore/pos-enterprise/issues/1234): [4] Initial config is not set up when pos start

v4.9.7
=============

#### Bug Fixed
1. [#1195](https://github.com/Magestore/pos-enterprise/issues/1195): [4] Action logs of place order aren't updated correct customer_id
2. [#1169](https://github.com/Magestore/pos-enterprise/issues/1169): [4] Display the wrong value of Opening Balance Session
3. [#1115](https://github.com/Magestore/pos-enterprise/issues/1115): [4] Duplicate session on a pos
4. [#1013](https://github.com/Magestore/pos-enterprise/issues/1013): [4] Error when install POS together with Magento 2.3.3 from beginning

#### Developer Issue
1. [#1210](https://github.com/Magestore/pos-enterprise/issues/1210): [4] Customers information in customer list aren't updated after place order in online mode

v4.9.6
=============

#### Bug Fixed
1. [#1191](https://github.com/Magestore/pos-enterprise/issues/1191): [4] Store credit is subtracted when an order placement fails
2. [#1187](https://github.com/Magestore/pos-enterprise/issues/1187): [4] [Bug] Require reason input when there is no different when closing session
3. [#1159](https://github.com/Magestore/pos-enterprise/issues/1159): [4] Empty value in "Category" column in Stock Value report

v4.9.5
=============

#### Feature Updated
1. [#1054](https://github.com/Magestore/pos-enterprise/issues/1054): [4] Tracking customer usage on POS

#### Bug Fixed
1. [#1153](https://github.com/Magestore/pos-enterprise/issues/1153): [4] Gift Card code form always show on checkout page even when disable it on settings.
2. [#1150](https://github.com/Magestore/pos-enterprise/issues/1150): [4] Show wrong qty in the Prepare Fulfil grid

v4.9.4
=============

#### Bug Fixed
1. [#1168](https://github.com/Magestore/pos-enterprise/issues/1168): [4] Function create invoice and shipment on POS is not stable
2. [#1166](https://github.com/Magestore/pos-enterprise/issues/1166): [4] Cannot export CSV file
3. [#1161](https://github.com/Magestore/pos-enterprise/issues/1161): [4] Unable to add new pages in POS's menu by extension
4. [#1156](https://github.com/Magestore/pos-enterprise/issues/1156): [4] Can not add gift code in front end
5. [#1154](https://github.com/Magestore/pos-enterprise/issues/1154): [4] Stock Adjustment neglects the update in product qty during POS sales

v4.9.3
=============

#### Bug Fixed
1. [#1142](https://github.com/Magestore/pos-enterprise/issues/1142): [4] Wrong Store name in receipt
2. [#1137](https://github.com/Magestore/pos-enterprise/issues/1137): [4] Cannot save barcode in the product detail page
3. [#1130](https://github.com/Magestore/pos-enterprise/issues/1130): [4] Blank page when refunding orders used store credit
4. [#1128](https://github.com/Magestore/pos-enterprise/issues/1128): [4] [Typo] Creating staff and email already exists
5. [#1122](https://github.com/Magestore/pos-enterprise/issues/1122): [4] Do NOT show supplier SKU when add products to Purchase Order
6. [#1121](https://github.com/Magestore/pos-enterprise/issues/1121): [4] Product Skus in supplier do NOT update when Magento product's SKU is updated
7. [#1105](https://github.com/Magestore/pos-enterprise/issues/1105): [4] Don't see customers on POS
8. [#1104](https://github.com/Magestore/pos-enterprise/issues/1104): [4] Can't Delete First Tracking Number of Shipment
9. [#1093](https://github.com/Magestore/pos-enterprise/issues/1093): [4] Order History will disappear from the list randomly
10. [#1088](https://github.com/Magestore/pos-enterprise/issues/1088): [4] Wrong Logo Size
11. [#1086](https://github.com/Magestore/pos-enterprise/issues/1086): [4] Currency isn't changed after change location.
12. [#1070](https://github.com/Magestore/pos-enterprise/issues/1070): [4] Cannot sync order if setup tax rate title for multiple store view.
13. [#1069](https://github.com/Magestore/pos-enterprise/issues/1069): [4] Store Credit disable Magento cache.

v4.9.2
=============

#### Bug Fixed
1. [#1092](https://github.com/Magestore/pos-enterprise/issues/1092): [4] POS stops loading at 80% when logging in
2. [#1017](https://github.com/Magestore/pos-enterprise/issues/1017): [4] Duplicate default payment when create new refund request
3. [#1015](https://github.com/Magestore/pos-enterprise/issues/1015): [4] Refunded payments without amount are still saved in Magento database

v4.9.1
=============

#### Bug Fixed
1. [#1076](https://github.com/Magestore/pos-enterprise/issues/1076): [4] Item price is wrong, in case the item has tier price
2. [#972](https://github.com/Magestore/pos-enterprise/issues/972): [4] The comment in order history is not correct after refunded the Stripe terminal transaction

#### Developer Issue
1. [#1081](https://github.com/Magestore/pos-enterprise/issues/1081): [4] Refactor ReactToPrint class to plugin print easier

v4.9.0
=============

#### Feature Updated
1. [#1021](https://github.com/Magestore/pos-enterprise/issues/1021): [4] Add Custom Discount for Items in POS

#### Bug Fixed
1. [#1062](https://github.com/Magestore/pos-enterprise/issues/1062): [4] Frontent - Missing shipping amount on Shopping Cart after applying gift card
2. [#1052](https://github.com/Magestore/pos-enterprise/issues/1052): [4] Cannot load Category with Store Credit module
3. [#1049](https://github.com/Magestore/pos-enterprise/issues/1049): [4] Can not save customer which has custom attribute
4. [#1046](https://github.com/Magestore/pos-enterprise/issues/1046): [4] Get wrong discount 

v4.8.2
=============

#### Bug Fixed
1. [#1019](https://github.com/Magestore/pos-enterprise/issues/1019): [4] Calculating remaining amount for payment in Refund Payment Method step is wrong
2. [#1008](https://github.com/Magestore/pos-enterprise/issues/1008): [4] Cannot load resource of Integration in Magento 2.3.3.
3. [#1006](https://github.com/Magestore/pos-enterprise/issues/1006): [4] Unused data in Magento core tables after install Store Credit.
4. [#956](https://github.com/Magestore/pos-enterprise/issues/956): [4] Webpos - Loading promotion icon is still displayed after set custom discount
5. [#937](https://github.com/Magestore/pos-enterprise/issues/937): [4] [Import barcode] Still show an error message when all barcode is imported successfully
6. [#921](https://github.com/Magestore/pos-enterprise/issues/921): [4] Can not create shipment for the item although it has the value of [Qty in Source] >0

v4.8.1
=============

#### Bug Fixed
1. [#995](https://github.com/Magestore/pos-enterprise/issues/995): [4] Performance - POS is slow down after running for a long time
2. [#989](https://github.com/Magestore/pos-enterprise/issues/989): [4] Check external stock is wrong
3. [#979](https://github.com/Magestore/pos-enterprise/issues/979): [4] POS Refund Zippay payment is wrong
4. [#977](https://github.com/Magestore/pos-enterprise/issues/977): [4] Not holding the custom price of the gift card product when checking out an on-hold order
5. [#975](https://github.com/Magestore/pos-enterprise/issues/975): [4] Display wrong stock of child product in configurable product if child product wasn't assigned to location
6. [#974](https://github.com/Magestore/pos-enterprise/issues/974): [4] Button scan barcode is not displayed on Ipad
7. [#961](https://github.com/Magestore/pos-enterprise/issues/961): [4] The config [Groups can use credit] of Store Credit is not working correctly on POS
8. [#954](https://github.com/Magestore/pos-enterprise/issues/954): [4] Store pickup - Unable to select store in edit pages of tag, schedule, holiday and special day.
9. [#935](https://github.com/Magestore/pos-enterprise/issues/935): [4] [Grid barcode generated history] Type of barcode created is wrong
10. [#933](https://github.com/Magestore/pos-enterprise/issues/933): [4] Product attributes do NOT appear when previewing barcode template

v4.8.0
=============

#### Feature Updated
1. [#878](https://github.com/Magestore/pos-enterprise/issues/878): [4] - Payment Offline module

#### Bug Fixed
1. [#963](https://github.com/Magestore/pos-enterprise/issues/963): [4] Show error on the Customer Credit Report page
2. [#959](https://github.com/Magestore/pos-enterprise/issues/959): [4] Reward point - Search box in page manage point balances isn't working
3. [#941](https://github.com/Magestore/pos-enterprise/issues/941): [4] There is no cash option when refund if order has been paid by zippay
4. [#925](https://github.com/Magestore/pos-enterprise/issues/925): [4] Cannot scroll customer list when using POS on big screen
5. [#922](https://github.com/Magestore/pos-enterprise/issues/922): [4] Sync API is duplicated

v4.7.0
=============

#### Feature Updated
1. [#185](https://github.com/Magestore/pos-enterprise/issues/185): [4] Integrate Stripe Terminal 

v4.6.4
=============

#### Bug Fixed
1. [#948](https://github.com/Magestore/pos-enterprise/issues/948): [4] POS wrong payment Icon 
2. [#939](https://github.com/Magestore/pos-enterprise/issues/939): [4] Zippay method is duplicated when select payment method in refund page
3. [#927](https://github.com/Magestore/pos-enterprise/issues/927): [4] Compatible with Magento 2.3.3

#### Developer Issue
1. [#943](https://github.com/Magestore/pos-enterprise/issues/943): [4] Refund payment template is difficult to customize

v4.6.3
=============

#### Bug Fixed
1. [#913](https://github.com/Magestore/pos-enterprise/issues/913): [4] [Barcode Success] 500 error when print barcode
2. [#899](https://github.com/Magestore/pos-enterprise/issues/899): [4] Can not add more items to cart after clicking on item at the first time
3. [#897](https://github.com/Magestore/pos-enterprise/issues/897): [4] POS is slow down when working in a long time
4. [#889](https://github.com/Magestore/pos-enterprise/issues/889): [4] Wrong URL when redirecting to POS
5. [#886](https://github.com/Magestore/pos-enterprise/issues/886): [4] POS - all versions - Barcode template jewelry does not work correctly
6. [#830](https://github.com/Magestore/pos-enterprise/issues/830): [4] Can NOT save tax order item to 'sales_order_tax_item' table
7. [#820](https://github.com/Magestore/pos-enterprise/issues/820): [4] Wrong default image url of Giftcard products

#### Developer Issue
1. [#911](https://github.com/Magestore/pos-enterprise/issues/911): [4] Can't plugin synchronize data

v4.6.2
=============

#### Bug Fixed
1. [#874](https://github.com/Magestore/pos-enterprise/issues/874): [4] Fulfillment_show the shipped items as prepare ship
2. [#873](https://github.com/Magestore/pos-enterprise/issues/873): [4] Fulfilment_show wrong value of [Total Items] in the [pick history] grid
3. [#868](https://github.com/Magestore/pos-enterprise/issues/868): [4] Can't synchronize customer data which has reward point
4. [#861](https://github.com/Magestore/pos-enterprise/issues/861): [4] No subtract Qty in Source when creating invoice virtual product from admin with order is placed on POS.
5. [#811](https://github.com/Magestore/pos-enterprise/issues/811): [4] Payment amount in order detail page on POS is wrong

#### Developer Issue
1. [#876](https://github.com/Magestore/pos-enterprise/issues/876): [4] Refactor code, add needed events to customize payment easier, support 4 last digits .

v4.6.1
=============

#### Bug Fixed
1. [#855](https://github.com/Magestore/pos-enterprise/issues/855): [4] 500 internal server when print barcode labels
2. [#835](https://github.com/Magestore/pos-enterprise/issues/835): [4] Error: Quote's payments are NOT cleared when update cart item
3. [#834](https://github.com/Magestore/pos-enterprise/issues/834): [POS 4] Error on Sort order online mode
4. [#832](https://github.com/Magestore/pos-enterprise/issues/832): [4] Can NOT create new customer on POS
5. [#827](https://github.com/Magestore/pos-enterprise/issues/827): [4] There are 3 files have the same class name "PaymentItem"
6. [#822](https://github.com/Magestore/pos-enterprise/issues/822): [4] Loading wrong region information of supplier
7. [#818](https://github.com/Magestore/pos-enterprise/issues/818): [4] Total items in Pick & Pack step is totally wrong
8. [#813](https://github.com/Magestore/pos-enterprise/issues/813): [4] Format date of delivery date is change when update it
9. [#810](https://github.com/Magestore/pos-enterprise/issues/810): [4] Email confirmation after creating a new order on POS show wrong payment methods

v4.6.0
=============

#### Feature Updated
1. [#249](https://github.com/Magestore/pos-enterprise/issues/249): [4] Complete shipment of order in POS

#### Bug Fixed
1. [#798](https://github.com/Magestore/pos-enterprise/issues/798): [4] Tính sai giá phí shipping khi trong cart có sản phẩm virtual
2. [#796](https://github.com/Magestore/pos-enterprise/issues/796): [4] Order bị complete (dù order chưa được ship) khi bật config cho phép Backorder (Magento)
3. [#795](https://github.com/Magestore/pos-enterprise/issues/795): [4] Lỗi về đồng bộ item trong order trên POS khi thực hiện fulfillment (pick, pack)
4. [#785](https://github.com/Magestore/pos-enterprise/issues/785): [4] Không add thêm được extension attributes vào order item
5. [#767](https://github.com/Magestore/pos-enterprise/issues/767): [POS Enterprise 4.x] - Lỗi không điền đc amount để refund 

v4.5.2
=============

#### Bug Fixed
1. [#789](https://github.com/Magestore/pos-enterprise/issues/789): [4] Chuyển đến trang frontend khi save barcode template với required field bị bỏ trống
2. [#781](https://github.com/Magestore/pos-enterprise/issues/781): [4] Lỗi ẩn input street khi edit billing ở store pickup
3. [#770](https://github.com/Magestore/pos-enterprise/issues/770): [4] Lỗi duplicate transaction 
4. [#763](https://github.com/Magestore/pos-enterprise/issues/763): [4] Không download được file log lỗi khi import transfer product
5. [#762](https://github.com/Magestore/pos-enterprise/issues/762): [4] Product cost không được lưu vào order 
6. [#754](https://github.com/Magestore/pos-enterprise/issues/754): [4] Remove some Print Options in Prepare Fulfill Step
7. [#748](https://github.com/Magestore/pos-enterprise/issues/748): [4] Lỗi không print được barcode có product attribute

v4.5.1
=============

#### Bug Fixed
1. [#739](https://github.com/Magestore/pos-enterprise/issues/739): [4] Không add to cart được product khi chưa sync xong
2. [#723](https://github.com/Magestore/pos-enterprise/issues/723): [4] Cannot add/edit Email template for PO

#### Developer Issue
1. [#755](https://github.com/Magestore/pos-enterprise/issues/755): [4] Add needed events, layout to customize easier
2. [#751](https://github.com/Magestore/pos-enterprise/issues/751): [4] Không link được store view tới location
3. [#749](https://github.com/Magestore/pos-enterprise/issues/749): [4] Mistake on client/pos/src/service/sales/OrderService.js

v4.5.0
=============

#### Feature Updated
1. [#131](https://github.com/Magestore/pos-enterprise/issues/131): [4] POS supports Multiple Websites

#### Bug Fixed
1. [#735](https://github.com/Magestore/pos-enterprise/issues/735): [4] Bug when open page prepare fulfil
2. [#724](https://github.com/Magestore/pos-enterprise/issues/724): [4] Sessions which were synced completely disappear on POS
3. [#720](https://github.com/Magestore/pos-enterprise/issues/720): [4] Thêm layout, class, event cho customize
4. [#719](https://github.com/Magestore/pos-enterprise/issues/719): [4] Mất product trong purchase order, stock adjust, stock taking khi xoá product
5. [#714](https://github.com/Magestore/pos-enterprise/issues/714): [4] Không tạo invoice, shipment khi checkout order có chứa sản phẩm virtual + simple/config...
6. [#691](https://github.com/Magestore/pos-enterprise/issues/691): [4] Lỗi không edit được billing address khi dùng storepickup

#### Developer Issue
1. [#716](https://github.com/Magestore/pos-enterprise/issues/716): [4] Need Index by row instead of re index all data of table after update

v4.4.8
=============

#### Bug Fixed
1. [#697](https://github.com/Magestore/pos-enterprise/issues/697): [4] Tự động tạo Adjustment Code khi chưa click btn [Start to Adjust Stock]
2. [#695](https://github.com/Magestore/pos-enterprise/issues/695): [4] Không hiện cột Location trong order grid 

v4.4.7
=============

#### Bug Fixed
1. [#671](https://github.com/Magestore/pos-enterprise/issues/671): [4] Show attribute là kiểu Dropdown bị sai khi print barcode.
2. [#670](https://github.com/Magestore/pos-enterprise/issues/670): [4] Bị lỗi khi scan sản phẩm config 2 lần ở cả online & offline mode
3. [#666](https://github.com/Magestore/pos-enterprise/issues/666): [4] Không tạo invoice được trong backend với order có storepickup
4. [#663](https://github.com/Magestore/pos-enterprise/issues/663): [4] Lỗi cập nhật customer khi tắt internet
5. [#659](https://github.com/Magestore/pos-enterprise/issues/659): [4] Lỗi khi disable Magestore_AdjustStock extension

#### Developer Issue
1. [#676](https://github.com/Magestore/pos-enterprise/issues/676): [4] Check sai điều kiện barcode làm chậm API search product trêm POS
2. [#673](https://github.com/Magestore/pos-enterprise/issues/673): [4] Giới hạn ký tự của Receipt Footer and Header quá ngắn.

v4.4.6
=============

#### Bug Fixed
1. [#646](https://github.com/Magestore/pos-enterprise/issues/646): [4] WebPOS Performance for Large Site
2. [#642](https://github.com/Magestore/pos-enterprise/issues/642): [4] Lỗi không hiển thị selected order ngay sau khi click vào order list
3. [#641](https://github.com/Magestore/pos-enterprise/issues/641): [4] PWA Không save được telephone trong customer popup
4. [#637](https://github.com/Magestore/pos-enterprise/issues/637): [4] Show sai giá trị trường Start date và End date khi tạo pricelist
5. [#633](https://github.com/Magestore/pos-enterprise/issues/633): [4] Hiển thị lệch 1 ngày trường Transfer Date khi delivered item sau khi tạo return request

#### Developer Issue
1. [#644](https://github.com/Magestore/pos-enterprise/issues/644): [4] Không tạo được Omc và bảng IndexedDb mới trong extension

v4.4.5
=============

#### Bug Fixed
1. [#625](https://github.com/Magestore/pos-enterprise/issues/625): [4] The string "Printed At: " is not translated (& solution)
2. [#620](https://github.com/Magestore/pos-enterprise/issues/620): [4] Bug Purchase Order - Receive item
3. [#613](https://github.com/Magestore/pos-enterprise/issues/613): [4] Lỗi tạo ra 1 empty row trong bảng os_supplier_product khi save product có supplier.

#### Developer Issue
1. [#621](https://github.com/Magestore/pos-enterprise/issues/621): [4] Epic của plugin không hoạt động sau khi build
2. [#528](https://github.com/Magestore/pos-enterprise/issues/528): [4] WebPOS Performance for Large Site

v4.4.4
=============

#### Bug Fixed
1. [#614](https://github.com/Magestore/pos-enterprise/issues/614): [4] fix show image in cart item when add products to cart which do not have image and Edit qty greater than 1 (solution like image of product listing)
2. [#608](https://github.com/Magestore/pos-enterprise/issues/608): [4] Cannot scan barcode after checkout
3. [#597](https://github.com/Magestore/pos-enterprise/issues/597): [POS Enterprise 4] Không tạo được order khi order total = 0 do cart rule discount 100%
4. [#596](https://github.com/Magestore/pos-enterprise/issues/596): [4] Nhiều button trên module supplier khi click bị save 2 lần.
5. [#582](https://github.com/Magestore/pos-enterprise/issues/582): [4] Compatible with new release of Magento 2.3.2

#### Developer Issue
1. [#616](https://github.com/Magestore/pos-enterprise/issues/616): [4] add more layout after cashier name in order history, order detail, print
2. [#605](https://github.com/Magestore/pos-enterprise/issues/605): [4] Không nhận css khi print receipt
3. [#589](https://github.com/Magestore/pos-enterprise/issues/589): [POS Enterprise 4] Add mixins for Class

4.4.3
=============

### Fixed Issues
1. [#579](https://github.com/Magestore/pos-enterprise/issues/579): [4] add some layouts, extension attribute
2. [#568](https://github.com/Magestore/pos-enterprise/issues/568): [4] Customize Issues
3. [#564](https://github.com/Magestore/pos-enterprise/issues/564): [4] Store Pickup- Trong trang checkout, không tự động show dropdown để có thể chọn được Store
4. [#561](https://github.com/Magestore/pos-enterprise/issues/561): [4] Redundant field: Shelf Location
5. [#558](https://github.com/Magestore/pos-enterprise/issues/558): [4] Bị lưu 2 lần khi save staff và pos
6. [#532](https://github.com/Magestore/pos-enterprise/issues/532): [4] Calculate tax wrongly when customer creates order online and selects Store pickup
7. [#522](https://github.com/Magestore/pos-enterprise/issues/522): [4] Can translate pick-up-at-store in the backend

4.4.2
=============

#### Bug Fixed
* [#536](https://github.com/Magestore/pos-enterprise/issues/536) -- [4] Reward Point: Hiển thị sai earning point message ở trang product detail khi có tier price
* [#551](https://github.com/Magestore/pos-enterprise/issues/551) -- [4] Chỉnh lại Phần tính toán qty to ship của bundle trong ShipmentRepository
* [#546](https://github.com/Magestore/pos-enterprise/issues/546) -- [4] Missing function sendBeforeExpireEmail
* [#537](https://github.com/Magestore/pos-enterprise/issues/537) -- [4] File EditPrice.js không hoạt động với plugin
* [#535](https://github.com/Magestore/pos-enterprise/issues/535) -- [4] Transfer Stock - Phần Type ở tất cả các chỗ trong dynamic grid đang chưa hiển thị đúng như trong magento
* [#530](https://github.com/Magestore/pos-enterprise/issues/530) -- [4] Remove Paypal Direct Payment Method
* [#526](https://github.com/Magestore/pos-enterprise/issues/526) -- [4] Transfer Stock_Không add product vào grid trong trường hợp chọn sản phẩm để receive từ nhiều trang
* [#524](https://github.com/Magestore/pos-enterprise/issues/524) -- [4] Transfer Stock_Khi chọn sản phẩm ở 2 trang liên tiếp => không show giá trị [Qty in Sending Source]
* [#523](https://github.com/Magestore/pos-enterprise/issues/523) -- [4] Transfer Stock_không nên cho receive tiếp sau khi đã receive trong trường hợp Qty to Send = 0
* [#512](https://github.com/Magestore/pos-enterprise/issues/512) -- [4] Sau khi đổi shipping method thì use point về 0


4.4.1
=============

#### Bug Fixed
* [#482](https://github.com/Magestore/pos-enterprise/issues/482) -- [4] Lỗi crash khi swipe card ở trang take payment
* [#487](https://github.com/Magestore/pos-enterprise/issues/487) -- [4] Error khi place order chọn shipping method: stock pickup
* [#491](https://github.com/Magestore/pos-enterprise/issues/491) -- [4] Không nên cho xóa sản phẩm custom sale
* [#498](https://github.com/Magestore/pos-enterprise/issues/498) -- [4] Lỗi khi save & apply Adjustment có SKU là số.
* [#502](https://github.com/Magestore/pos-enterprise/issues/502) -- [4] 1 số file không hỗ trợ plugin
* [#517](https://github.com/Magestore/pos-enterprise/issues/517) -- [4] Thêm layout cho file view/component/checkout/cart/CartItem.js

4.4.0
=============

#### Feature Updated
* [#443](https://github.com/Magestore/pos-enterprise/issues/443) -- [4] Build Transfer Stock feature in Magento MSI

#### Bug Fixed
* [#455](https://github.com/Magestore/pos-enterprise/issues/455) -- [4] Lỗi hiện thị thông tin shipping method với order có shipment là store pickup
* [#473](https://github.com/Magestore/pos-enterprise/issues/473) -- [4] Lỗi với google suggest
* [#489](https://github.com/Magestore/pos-enterprise/issues/489) -- [4] Store Pickup_hiển thị sai Order ID khi view detail của store

4.3.1
=============

#### Bug Fixed
* [#440](https://github.com/Magestore/pos-enterprise/issues/440) -- [4] giá trị [Salable Qty] bị cộng lại vào và không trừ giá trị [Qty in Source] sau khi tạo shipment
* [#446](https://github.com/Magestore/pos-enterprise/issues/446) -- [4] Header và Footer trong receipt của POS không in ra được kí tự xuống dòng
* [#448](https://github.com/Magestore/pos-enterprise/issues/448) -- [4] Don't show thumbnail of product when Generateing Barcode
* [#459](https://github.com/Magestore/pos-enterprise/issues/459) -- [4] Không thể đồng bộ order từ POS lên server khi tạo order với sản phẩm configurable

4.3.0
=============

#### Feature Updated
* [#130](https://github.com/Magestore/pos-enterprise/issues/130) -- [4] Fulfill POS Orders from multiple sources
* [#134](https://github.com/Magestore/pos-enterprise/issues/134) -- [4] Receipt configurator
* [#415](https://github.com/Magestore/pos-enterprise/issues/415) -- [4] Check external stock
* [#393](https://github.com/Magestore/pos-enterprise/issues/393) -- [4] Thêm một icon loading cho phần Discount để staff có thể nhận biết discount đang được tính toán

#### Bug Fixed
* [#423](https://github.com/Magestore/pos-enterprise/issues/423) -- [4] Lỗi customer popup tự động save/tắt khi blur field email
* [#417](https://github.com/Magestore/pos-enterprise/issues/417) -- [4] Báo sai message khi scan không tìm được pack items
* [#416](https://github.com/Magestore/pos-enterprise/issues/416) -- [4] Lỗi giao diện order fullfilment
* [#414](https://github.com/Magestore/pos-enterprise/issues/414) -- [4] Adjust stock bị treo khi import 2000 SKUs
* [#412](https://github.com/Magestore/pos-enterprise/issues/412) -- [4] Lỗi đồng bộ customer khi có cache
* [#404](https://github.com/Magestore/pos-enterprise/issues/404) -- [4] Auto create shipment for backsales order although there is no qty in magento ( warehouse )
* [#394](https://github.com/Magestore/pos-enterprise/issues/394) -- [4] Store Credit làm chết Elasticsearch của Magento.

4.2.2
=============

#### Feature Updated

#### Bug Fixed
* [#396](https://github.com/Magestore/pos-enterprise/issues/396) -- [4] PrintComponent không thể plugin hoặc rewrite được.
* [#393](https://github.com/Magestore/pos-enterprise/issues/393) -- [4] Thêm một icon loading cho phần Discount để staff có thể nhận biết discount đang được tính toán
* [#388](https://github.com/Magestore/pos-enterprise/issues/388) -- [4] Sửa lại text Created At thành Create On trong trang detail của Adjustment
* [#386](https://github.com/Magestore/pos-enterprise/issues/386) -- [Stock Adjustment M2.3] Trỏ chuột khi nhấn Scan barcode
* [#346](https://github.com/Magestore/pos-enterprise/issues/346) -- [4] Phần permission cho POS gây nhiều hiểu lầm khi sử dụng

4.2.1
=============

#### Feature Updated
* [#364](https://github.com/Magestore/pos-enterprise/issues/364) -- [4] Update Icon of POS in Magento Backend
* [#363](https://github.com/Magestore/pos-enterprise/issues/363) -- [4] Update Icon of Inventory Management in Magento Backend

#### Bug Fixed
* [#380](https://github.com/Magestore/pos-enterprise/issues/380) -- [4] [Dropship] - Error khi view dropship
* [#369](https://github.com/Magestore/pos-enterprise/issues/369) -- [4] Không thể scan barcode online trên POS với magento v2.3.1
* [#360](https://github.com/Magestore/pos-enterprise/issues/360) -- [4] Không add đủ product vào cart khi scan barcode quá nhanh
* [#358](https://github.com/Magestore/pos-enterprise/issues/358) -- [4] 500 error khi hold order

4.2.0
=============

#### Feature Updated
* [#203](https://github.com/Magestore/pos-enterprise/issues/203) -- [4] Built inventory management functional with magento MSI: Adjust stock

#### Bug Fixed
* [#352](https://github.com/Magestore/pos-enterprise/issues/352) -- [4] Có thể view bất kì drop ship nào không thuộc quyền quản lý của supplier
* [#333](https://github.com/Magestore/pos-enterprise/issues/333) -- [4]_Return Request: hiển thị sai product list khi thực hiện chọn các sp trong [All Supplier Products]
* [#335](https://github.com/Magestore/pos-enterprise/issues/335) -- [4] Lỗi bị duplicate item trong trang view packed items
* [#347](https://github.com/Magestore/pos-enterprise/issues/347) -- [4] Show sai qty khi in Picked items và ở 1 vài chỗ
* [#349](https://github.com/Magestore/pos-enterprise/issues/349) -- [4] Sau khi order sp bundle, khi in receipt không show các thông tin: Qty, Price, Subtotal

4.1.8
=============

#### Feature Updated

#### Bug Fixed
* [#324](https://github.com/Magestore/pos-enterprise/issues/324) -- [4] - Barcode in ra từ trang barcode detail và trang product detail khác nhau
* [#316](https://github.com/Magestore/pos-enterprise/issues/316) -- [4] Lỗi liên quan đến fulfilment khi order sản phẩm config, bundle trên POS
* [#313](https://github.com/Magestore/pos-enterprise/issues/313) -- [4] Fulfillment_lỗi template trong trang Pack items
* [#303](https://github.com/Magestore/pos-enterprise/issues/303) -- [4] Không thể save được barcode trong trang product detail

4.1.7
=============

#### Feature Updated
* [#230](https://github.com/Magestore/pos-enterprise/issues/230) -- [4] Compatible with new release Magento 2.3.1

#### Bug Fixed
* [#290](https://github.com/Magestore/pos-enterprise/issues/290) -- [4] Gift card_lỗi hiển thị sai price khi reorder
* [#292](https://github.com/Magestore/pos-enterprise/issues/292) -- [4] Authorize.net trên POS luôn chạy link sandbox ngay cả khi disable sandbox
* [#288](https://github.com/Magestore/pos-enterprise/issues/288) -- [4] Gift Card_Hiển thị sai price
* [#285](https://github.com/Magestore/pos-enterprise/issues/285) -- [4] Hiển thị sai số point balance trên receipt
* [#282](https://github.com/Magestore/pos-enterprise/issues/282) -- [4] Reward Point_ trường "Limit value allowed to spend points at" trong setting "Spending Rate" không work
* [#274](https://github.com/Magestore/pos-enterprise/issues/274) -- [4] Không hiện shipping method khi nhập trường handling fee
* [#235](https://github.com/Magestore/pos-enterprise/issues/235) -- [4] WebPRNT barcode doesn't present on MacOS

4.1.6
=============

#### Feature Updated
* [#222](https://github.com/Magestore/pos-enterprise/issues/222) -- [4] Chuyển Reward Point thành plugin

#### Bug Fixed
* [#265](https://github.com/Magestore/pos-enterprise/issues/265) -- [4] Lỗi apply catalog price rule không đúng theo timezone setting trong server
* [#260](https://github.com/Magestore/pos-enterprise/issues/260) -- [4] POS ở phần backend không có file translate mẫu
* [#257](https://github.com/Magestore/pos-enterprise/issues/257) -- [4] Lỗi hiển thị ảnh option của sản phẩm configurable product
* [#253](https://github.com/Magestore/pos-enterprise/issues/253) -- [4] Hiển thị giá sai khi hold order với bundle product

4.1.5
=============

#### Feature Updated

#### Bug Fixed
* [#242](https://github.com/Magestore/pos-enterprise/issues/242) -- [4] Lỗi API đồng bộ order, khi order chưa product đã bị xóa
* [#240](https://github.com/Magestore/pos-enterprise/issues/240) -- [4] Zip/Postal code không required khi nhấn Save (mặc dù đây là trường required)
* [#239](https://github.com/Magestore/pos-enterprise/issues/239) -- [4] Hiển thị số 0 khi Category không có dữ liệu
* [#238](https://github.com/Magestore/pos-enterprise/issues/238) -- [4] Order bị lỗi khi checkout với sản phẩm configurable được tạo mới
* [#232](https://github.com/Magestore/pos-enterprise/issues/232) -- [POS Enterprise 4] Print Refund Receipt - Hiển thị sai qty sản phẩm được refund
* [#221](https://github.com/Magestore/pos-enterprise/issues/221) -- [4] Error in generate CSV file to download - Barcode Management

4.1.4
=============

#### Feature Updated

#### Bug Fixed
* [#219](https://github.com/Magestore/pos-enterprise/issues/219) -- [4] Hiển thị trang trắng khi view product trên PWA POS (fixed in PR [#226](https://github.com/Magestore/pos-enterprise/pull/226))
* [#217](https://github.com/Magestore/pos-enterprise/issues/217) -- [3] Không hiển thị tên sản phẩm ở list product trên ipad (fixed in PR [#228](https://github.com/Magestore/pos-enterprise/pull/228))
* [#215](https://github.com/Magestore/pos-enterprise/issues/215) -- [4] Webpos Shipping method should implement interface CarrierInterface (fixed in PR [#224](https://github.com/Magestore/pos-enterprise/pull/224))
* [#190](https://github.com/Magestore/pos-enterprise/issues/190) -- [4] WebPRNT can not work on https website (fixed in PR [#234](https://github.com/Magestore/pos-enterprise/pull/234))

4.1.3
=============

#### Bug Fixed
1. [#210](https://github.com/Magestore/pos-enterprise/issues/210): [4] [POS] Không thể print được order receipt và refund receipt


4.1.2
=============

#### Bug Fixed
1. [#201](https://github.com/Magestore/pos-enterprise/issues/201): [4] [Receipt] Cần hiển thị receipt trên nhiều trang nếu order có nhiều products
2. [#150](https://github.com/Magestore/pos-enterprise/issues/150): [4] Lỗi đồng bộ Order với những site không có quyền ghi vào thư mục generated sau khi đã chạy di:compile
3. [#165](https://github.com/Magestore/pos-enterprise/issues/165): [POS Enterprise 4] Tab Dropship trong order view không hiển thị dropship
4. [#187](https://github.com/Magestore/pos-enterprise/issues/187): [4] Lỗi hiển thị ở tax summary khi có trên 3 tax rules được apply
1. [#198](https://github.com/Magestore/pos-enterprise/issues/198): [4] Thêm cơ chế Plugin cho POS Client JS

4.1.1
=============

#### Bug Fixed
1. [#172](https://github.com/Magestore/pos-enterprise/issues/172): [4] Cài Magento từ đầu khi có các module POS của Magestore sẽ lỗi (fixed in PR [#181](https://github.com/Magestore/pos-pro/pull/181))
2. [#169](https://github.com/Magestore/pos-enterprise/issues/169): [4] Bug không tìm thấy class được khai báo trong file xml (fixed in PR [#180](https://github.com/Magestore/pos-pro/pull/180))
3. [#159](https://github.com/Magestore/pos-enterprise/issues/159): [4] Lỗi SQL Injection của Store Pickup (fixed in PR [#161](https://github.com/Magestore/pos-pro/pull/161))
4. [#149](https://github.com/Magestore/pos-enterprise/issues/149): [4] Display full tax summary (fixed in PR [#167](https://github.com/Magestore/pos-pro/pull/167))
5. [#146](https://github.com/Magestore/pos-enterprise/issues/146): [4] Bug vòng lặp vô hạn khi select config "Use the attribute as barcode" của module barcode. (fixed in PR [#173](https://github.com/Magestore/pos-pro/pull/173))
6. [#142](https://github.com/Magestore/pos-enterprise/issues/142): [4] Không filter được gift code history theo status (fixed in PR [#156](https://github.com/Magestore/pos-pro/pull/156))

4.1.0
=============

### Features
* [#114](https://github.com/Magestore/pos-pro/issues/114): [4] Insert Order Comment during Checkout
* [#113](https://github.com/Magestore/pos-pro/issues/113): [4] Improve UX of Numpad
* [#98](https://github.com/Magestore/pos-pro/issues/98): Hiển tên sản phẩm dài trên POS
* [#90](https://github.com/Magestore/pos-pro/issues/90): [4] Disable Customer icon in checkout page
* [#88](https://github.com/Magestore/pos-pro/issues/88): [4] Hiển thị customer telephone trên order detail
* [#85](https://github.com/Magestore/pos-pro/issues/85): [4] Scan barcode tự động không cần click vào input

### Fixed Bugs
* [#111](https://github.com/Magestore/pos-pro/issues/111): [POS Enterprise 4] Hiển thị sai thông tin của location ở trong reprint receipt
* [#83](https://github.com/Magestore/pos-pro/issues/83): [4] Bug lấy thiếu dữ liệu category khi site có quá nhiều category
* [#86](https://github.com/Magestore/pos-pro/issues/86): [POS Enterprise 4] Dropship_ available qty bị sai khi người dùng double click vào btn [Back to Fulfill] trong backend hoặc btn [Cancel] trên frontend để thực hiện nghiệp vụ cancel dropship request
* [#65](https://github.com/Magestore/pos-pro/issues/65): [Pos Enterprise 4] FulfilSuccess Step Delivery packages scan 1 giá trị tracking number không hợp lệ bị hiện blank modal
* [#96](https://github.com/Magestore/pos-pro/issues/96): [4] [Dropship] Cannot create shipment from dropship request on backend
* [#64](https://github.com/Magestore/pos-pro/issues/64): [Pos Enterprise 4] FulfilSuccess không reload lại grid picked, packed sau khi thực hiện action pick/ pack

4.0.1
=============

#### Bug Fixed
1. [#72](https://github.com/Magestore/pos-enterprise/issues/72): [4] Bug Fix cứng base url của PWA POS là pub/apps/pos
2. [#77](https://github.com/Magestore/pos-enterprise/issues/77): [4] Hiển thị số tiền thanh toán trong payment list ở trang order detail vượt quá số tiền thực tế nhận được từ khách
3. [#102](https://github.com/Magestore/pos-enterprise/issues/102): [4] Không tự động send emails sau khi order completed

4.0.0
=============

- Initial release POS Enterprise 4
- Compatible with Magento 2.3.x
