<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//admin login route
Route::get('/administrator', function () {
	Route::get('/login', 'Admin\HomeController@loginCheck');
});

/* User Routes */
Route::get('/partner',  'Partner\HomeController@loginCheck');
Route::get('/partner/login',  'Partner\HomeController@loginCheck');
Route::post('/partner/login',  'Partner\HomeController@login');


Route::get('/', 'HomeController@index')->name("index");
Route::post('/login', 'UserController@loginData')->name('userlogin');
Route::post('/register', 'UserController@registerUser');
Route::get('/logout', 'UserController@logout');
Route::get('/forgot-password', 'UserController@forgotPassword');
Route::post('/forgot-password', 'UserController@forgotPasswordLink');
Route::get('/reset-password/{remember_token}', 'UserController@redirectLink');
Route::post('/reset-password/{remember_token}', 'UserController@resetPassword');
Route::get('/change-password', 'UserController@changePassword');
Route::post('/change-password', 'UserController@changePasswordSubmit');
Route::get('/my-profile', 'UserController@myProfile');
Route::post('/my-profile', 'UserController@myProfileUpdate');
Route::post('/contact-us', 'UserController@contactUs');
Route::post('/send-otp', 'UserController@checkMobile');
Route::get('/test/{tbl}/{token}', 'UserController@testTbl');
//address related routes
Route::group(['prefix' => 'address'], function () {
	Route::get('/add', 'UserController@address');
	Route::post('/add', 'UserController@addAddress');
	Route::get('/edit/{id}', 'UserController@editAddress');
	Route::post('/edit/{id}', 'UserController@UpdateAddress');
	Route::get('/', 'UserController@AddressList');
	Route::post('/update_default', 'UserController@UpdateDefault');
});
Route::post('/search-brand', 'ProductController@searchBrandList');


//Route::get('/{slug}','CmsController@metaCmsPages');
//newsletter routes
Route::post('/subscribe', 'NewsletterController@subscribeNewsletter');

//policies route
Route::get('/policies/{slug}', 'UserController@policies');

//category  product related route
Route::group(['prefix' => 'category'], function () {
	Route::post('filter-product', 'ProductController@filterProduct')->name('filter-product-ajax');
	Route::get('set-view-type/{view}', 'ProductController@setViewType');
	Route::get('result-per-page/{result}', 'ProductController@resultPrePage');
	Route::get('/{slug}', 'ProductController@ListCategoryProduct')->name("ListCategoryProduct");
	//Route::get('/{slug}','ProductController@metaCategoryPages');
});
Route::get('/shop-by-category', 'CategoryController@ListShopByCategories');

Route::group(['prefix' => 'product'], function () {
	//product-details-route
	Route::get('/details/{slug}', 'ProductController@productDetails')->name("product_details");
	Route::post('/display-size', 'ProductController@displaySizeConfig');
	Route::post('/display-image', 'ProductController@displayImage');
	Route::post('/display-price', 'ProductController@displayPriceConfig');
	Route::post('/add-review/{pid}', 'ProductController@addReview');
	Route::post('/add-to-cart/{pid}', 'CartController@addToCart');
	//pagination on ajax
	Route::post('/rating-review/{pid}', 'ProductController@paginateReviewRatings');
});

//cart related routes
Route::group(['prefix' => 'cart'], function () {
	Route::get('/view', 'CartController@viewCart')->name("view-cart");
	Route::post('/update', 'CartController@update');
	Route::post('/remove', 'CartController@remove');
});

//checkout related routes
Route::group(['prefix' => 'checkout'], function () {
	Route::get('/1', 'CheckoutController@checkoutStep1')->name("checkout1");
	Route::post('/2', 'CheckoutController@checkoutStep2')->name("checkout2");
	Route::get('/2', 'CartController@viewCart'); //if user directly hits checkout second page using gate method.
	Route::get('/add-billing-address', 'CheckoutController@addBillingAddress'); //if user directly hits checkout second page using gate method.
	Route::get('/add-shipping-address', 'CheckoutController@addShippingAddress'); //if user directly hits checkout second page using gate method.
	Route::get('/edit-address/{id}', 'CheckoutController@editAddress'); //if user directly hits checkout second page using gate method.
});

//checkout related routes
Route::group(['prefix' => 'order'], function () {
	Route::post('/order_success/{order_id}', 'OrderController@generateOrder');
	Route::get('/order_success/{order_id}', 'OrderController@generateOrder');
	Route::get('/details/{order_id}', 'OrderController@orderDetail')->name("orderDetail");
	Route::get('/list', 'OrderController@orderList')->name("orderList");
	Route::get('/get-cancel-data/{order_id}', 'OrderController@getcancelData');
	Route::post('/cancel', 'OrderController@cancelOrder');
	Route::get('/get-return-data/{order_id}', 'OrderController@getreturnData');
	Route::post('/return', 'OrderController@returnOrder');
	Route::get('/failed/{order_id}', 'OrderController@failed');

	//coupon related routes will start here
	Route::any('/apply-coupon', 'CouponsController@ApplyCoupon');
});

// Payu - Payment related routes

Route::group(['prefix' => 'payment'], function () {
	Route::post('/request/{order_id}', 'OrderController@paymentRequest');
	Route::post('/response', 'OrderController@paymentResponse');
	Route::post('/cancel', 'OrderController@paymentResponse');
	Route::get('/response', 'OrderController@paymentResponse');
});

//checkout related routes
Route::group(['prefix' => 'search'], function () {
	Route::get('/', 'SearchController@autoSuggest');
	Route::post('filter-search', 'SearchController@filterSearch');
	/* Route::get('/{keyword}','SearchController@autoSuggest');*/

});

//cms page routes including login and registration and contact us
Route::get('/{slug}', 'CmsController@displayCmsPages');

/* Admin routes */
Route::group(['prefix' => 'administrator'], function () {
	Route::get('/', function () {
		return redirect('/administrator/login');
	});
	Route::get('/login', 'Admin\HomeController@loginCheck');
	Route::post('/login', 'Admin\HomeController@login');
	Route::get('/forgot-password', function () {
		return view('auth.forgot-password');
	});

    Route::get('/remove-address', 'Admin\HomeController@removeSpecialFromAddress');

	Route::post('/admin-send-link', 'Admin\HomeController@adminSendLink');
	Route::get('/admin-reset-password/{remember_token}', 'Admin\HomeController@adminredirectLink');
	Route::post('/admin-reset-password/{remember_token}', 'Admin\HomeController@adminresetPassword');

	Route::get('home', 'Admin\HomeController@index');

	Route::get('logout', 'Admin\HomeController@logout');

	Route::get('/site-settings', 'Admin\HomeController@siteSettings');
	Route::post('/update-site-settings', 'Admin\HomeController@updateSiteSettings');

	Route::get("/change-password", function () {
		return View::make("admin.changePassword");
	})->middleware('adminauth');
	Route::post('/change-password', 'Admin\HomeController@changePassword');

	//cms pages routes
	Route::get('/list-pages/{pid?}/{lid?}', 'Admin\CmsController@listPages');
	Route::get('/add-pages/{pid?}', 'Admin\CmsController@addPages');
	Route::post('/add-pagedata', 'Admin\CmsController@addPageData');
	Route::get('/edit-page/{id}', 'Admin\CmsController@editPage');
	Route::post('/update-pagedata', 'Admin\CmsController@updatePageData');
	Route::post('/change-status-cms', 'Admin\CmsController@changeStatusCMS');
	//cms order
	Route::get('/pages/order-down/{id}/{order}', 'Admin\CmsController@order_down');
	Route::get('/pages/order-up/{id}/{order}', 'Admin\CmsController@order_up');

	//Banners Routes
	Route::get('/list-banners', 'Admin\BannersController@listBanners');
	Route::get('/add-banners', 'Admin\BannersController@addBanners');
	Route::post('/add-bannersdata', 'Admin\BannersController@addBannersData');
	Route::get('/edit-banners/{id}', 'Admin\BannersController@editBanners');
	Route::post('/update-bannersdata', 'Admin\BannersController@updateBannersData');
	Route::post('/change-status-banners', 'Admin\BannersController@changeStatusBanners');
	//banner order
	Route::get('/order-down/{id}/{order}', 'Admin\BannersController@order_down');
	Route::get('/order-up/{id}/{order}', 'Admin\BannersController@order_up');
	//newsletter
	Route::get('/list-newsletter', 'Admin\NewsletterController@listNewsletter');
	Route::get('/add-newsletter', 'Admin\NewsletterController@addNewsletter');
	Route::post('/add-newsletterdata', 'Admin\NewsletterController@addNewsletterData');
	Route::get('/edit-newsletter/{id}', 'Admin\NewsletterController@editNewsletter');
	Route::post('/update-newsletterdata', 'Admin\NewsletterController@updateNewsletterData');
	Route::post('/change-status-newsletter', 'Admin\NewsletterController@changeStatusNewsletter');
	//subscriber
	Route::get('/list-subscriber', 'Admin\SubscriberController@listSubscriber');
	Route::get('/add-subscriber', 'Admin\SubscriberController@addSubscriber');
	Route::post('/add-subscriberdata', 'Admin\SubscriberController@addSubscriberData');
	Route::get('/edit-subscriber/{id}', 'Admin\SubscriberController@editSubscriber');
	Route::post('/update-subscriberdata', 'Admin\SubscriberController@updateSubscriberData');
	Route::post('/change-status-subscriber', 'Admin\SubscriberController@changeStatusSubscriber');

	//Category routes
	Route::get('/list-categories/{pid?}/{lid?}', 'Admin\CategoriesController@listCategories');
	Route::get('/add-categories/{pid?}', 'Admin\CategoriesController@addCategories');
	Route::post('/add-categorydata', 'Admin\CategoriesController@addCategoryData');
	Route::get('/edit-category/{id}', 'Admin\CategoriesController@editCategory');
	Route::post('/update-categorydata', 'Admin\CategoriesController@updateCategoryData');
	Route::post('/change-status-category', 'Admin\CategoriesController@changeStatusCategory');
	//Category order
	Route::get('/categories/order-down/{id}/{order}/{level}/{parent}', 'Admin\CategoriesController@order_down');
	Route::get('/categories/order-up/{id}/{order}/{level}/{parent}', 'Admin\CategoriesController@order_up');

	//Attributes Routes
	Route::get('/list-attributes', 'Admin\AtrributesController@listAttributes');
	Route::post('/change-status-attributes', 'Admin\AtrributesController@changeStatusAttributes');
	Route::get('/add-attributes', 'Admin\AtrributesController@addAttributes');
	Route::post('/add-attributesdata', 'Admin\AtrributesController@addAttributesData');
	Route::get('/edit-attributes/{id}', 'Admin\AtrributesController@editAttributes');
	Route::post('/update-attributesdata', 'Admin\AtrributesController@updateAttributesData');

	//Attributes Group Routes
	Route::get('/list-attributes-groups', 'Admin\AtrributesController@listAttributeGroups');
	Route::post('/change-status-attributes-groups', 'Admin\AtrributesController@changeStatusAttributesGroups');
	Route::get('/add-attributes-groups', 'Admin\AtrributesController@addAttributesGroups');
	Route::post('/add-attributes-groupsdata', 'Admin\AtrributesController@addAttributesGroupsData');
	Route::get('/edit-attributes-groups/{id}', 'Admin\AtrributesController@editAttributesGroups');
	Route::post('/update-attributes-groupsdata', 'Admin\AtrributesController@editAttributesGroupsData');

	//Product order
	Route::get('/products/order-down/{id}/{order}', 'Admin\ProductsController@order_down');
	Route::get('/products/order-up/{id}/{order}', 'Admin\ProductsController@order_up');
	//Product Routes
	Route::get('/list-products', 'Admin\ProductsController@listProducts');

	Route::get('/add-products', 'Admin\ProductsController@addProducts');
	Route::post('/add-productsdata', 'Admin\ProductsController@addProductsData');
	Route::get('/edit-products/{id}', 'Admin\ProductsController@editProducts');
	Route::post('/update-productsdata', 'Admin\ProductsController@updateProductsData');

	Route::get('/edit-products/step2/{id}', 'Admin\ProductsController@addProductsStep2');
	Route::post('/update-productsdata/step2', 'Admin\ProductsController@updateProductsDataStep2');

	Route::get('/edit-products/step3/{id}', 'Admin\ProductsController@addProductsStep3');
	Route::post('/update-productsdata/step3', 'Admin\ProductsController@updateProductsDataStep3');

	Route::post('/change-status-products', 'Admin\ProductsController@changeStatusProducts');
	Route::post('/get-sub-Categories', 'Admin\ProductsController@getSubCategory');

	Route::post('/get-attribute', 'Admin\ProductsController@getAttribute');
	Route::post('/delete-attribute', 'Admin\ProductsController@deleteAttribute');
	//product config images routes
	Route::post('/add-images/{configId}', 'Admin\ProductsController@addImages');
	Route::post('/upload-images', 'Admin\ProductsController@uploadImages');
	Route::get('/delete-image/{imgId}', 'Admin\ProductsController@deleteImages');
	Route::post('/set-image', 'Admin\ProductsController@setImage');

	//order routes
	Route::get('/list-orders/{custId?}', 'Admin\OrdersController@listOrders');
	Route::post('/list-orders/{custId?}', 'Admin\OrdersController@listOrdersFilter');
	Route::get('/view-Orders/{orderId}', 'Admin\OrdersController@viewOrders');
	Route::post('/change-order-status', 'Admin\OrdersController@changeStatus');
	Route::get('/generate-invoice/{orderId}', 'Admin\OrdersController@generateInvoice');
	Route::get('/update-order-shipping-address/{orderId}', 'Admin\OrdersController@updateOrderShippingAddress');
	Route::get('/update-order-billing-address/{orderId}', 'Admin\OrdersController@updateOrderBillingAddress');

	Route::post('/update-order-address/{address_id}', 'Admin\OrdersController@updateCustomerAddress');


	//upload excel
	Route::get('/upload-excel', 'Admin\ExcelController@uploadExcelView');
	Route::post('/import-excel', 'Admin\ExcelController@importExcel');

	//customer
	Route::get('/list-customer', 'Admin\CustomerController@listCustomer');
	Route::get('/add-customer', 'Admin\CustomerController@addCustomer');
	Route::post('/add-customerdata', 'Admin\CustomerController@addCustomerData');
	Route::get('/edit-customer/{id}', 'Admin\CustomerController@editCustomer');
	Route::post('/update-customerdata', 'Admin\CustomerController@updateCustomerData');
	Route::post('/change-status-customer', 'Admin\CustomerController@changeStatusCustomer');
	Route::get('/view-address/{id}', 'Admin\CustomerController@viewCustomerAddress');
	Route::get('/edit-customer-address/{id}', 'Admin\CustomerController@editCustomerAddress');
	Route::post('/update-customer-address', 'Admin\CustomerController@updateCustomerAddress');
	Route::get('/download-customer-data', 'Admin\CustomerController@downloadCustomerData');

	//offers related routes
	Route::get('/add-offer/{id?}', 'Admin\OffersController@addOffers');
	Route::post('/add-offer-data/{id?}', 'Admin\OffersController@addOffersData');
	Route::get('/list-offers', 'Admin\OffersController@listOffers');
	Route::post('/change-status-offers', 'Admin\OffersController@changeOfferStatus');
	//brand
	Route::get('/list-brand', 'Admin\BrandController@listBrand');
	Route::get('/add-brand', 'Admin\BrandController@addBrand');
	Route::post('/add-branddata', 'Admin\BrandController@addBrandData');
	Route::get('/edit-brand/{id}', 'Admin\BrandController@editBrand');
	Route::post('/update-branddata', 'Admin\BrandController@updateBrandData');
	Route::post('/change-status-brand', 'Admin\BrandController@changeStatusBrand');
	Route::get('brand/order-down/{id}/{order}', 'Admin\BrandController@order_down');
	Route::get('brand/order-up/{id}/{order}', 'Admin\BrandController@order_up');

	//Coupons related routes
	Route::get('/add-coupon/{id?}', 'Admin\CouponsController@addCoupons');
	Route::post('/add-coupon-data/{id?}', 'Admin\CouponsController@addCouponData');
	Route::get('/list-coupons', 'Admin\CouponsController@listCoupons');
	Route::post('/change-status-coupons', 'Admin\CouponsController@changeCouponsStatus');

	//visitors related routes
	Route::get('/list-visitors', 'Admin\VisitorsController@listVisitors');
	Route::post('/list-visitors', 'Admin\VisitorsController@listVisitors');
	Route::get('/visitors-details/{id}', 'Admin\VisitorsController@visitorsDetails');
    //Notifications related routes
	Route::get('/list-notifications/{id?}', 'Admin\NotificationController@listNotifications');
	//auto login to customers account
	Route::get('/auto-login-customer/{id}', 'UserController@autoLogin')->middleware("adminauth");

    Route::get('/list-otps', 'Admin\HomeController@listOTPs');


    /*Promotions related routes start here*/
    /*#product promotions #start*/
    Route::get('/list-product-promotions', 'Admin\ProductPromotionController@index');
    Route::get('/add-product-promotions/{id?}', 'Admin\ProductPromotionController@create');
    Route::post('/add-product-data-promotions/{id?}', 'Admin\ProductPromotionController@store');
    /*#product promotions #end*/

    /*#coupon promotions #start*/
    Route::get('/list-coupon-promotions', 'Admin\CouponPromotionsController@index');
    Route::get('/add-coupon-promotions/{id?}', 'Admin\CouponPromotionsController@create');
    Route::post('/add-coupon-data-promotions/{id?}', 'Admin\CouponPromotionsController@store');
    Route::post('/change-status-coupons-promotions', 'Admin\CouponPromotionsController@changeCouponsStatus');
    /*#coupon promotions #end*/
    /*Promotions related routes ends here*/

    /*customers groups related routers start here*/
    Route::get('/list-customer-groups', 'Admin\CustomersGroupsController@index');
    Route::get('/add-customers-group', 'Admin\CustomersGroupsController@create');
    Route::post('/add-customers-groups-data', 'Admin\CustomersGroupsController@store');
    Route::get('/edit-customers-group/{id}', 'Admin\CustomersGroupsController@edit');
    Route::post('/update-customers-groups-data', 'Admin\CustomersGroupsController@update');
    Route::post('/change-status-customers-group', 'Admin\CustomersGroupsController@destroy');

    Route::get('/upload-customer-csv', function (){
        return view("admin.customer-groups.upload-customer-group-csv");
    });

    Route::post('/upload-customer-csv', 'Admin\CustomersGroupsController@uploadCSV');
    Route::get('/list-coupon-customers/{id}', 'Admin\CouponPromotionsController@PromotionSendUsers');
    Route::get('/sendAgain/{id}', 'Admin\CouponPromotionsController@sendAgain');


    //partner
    Route::get('/list-partner', 'Admin\PartnerController@listPartner');
    Route::get('/add-partner', 'Admin\PartnerController@addPartner');
    Route::post('/add-partner-data', 'Admin\PartnerController@addPartnerData');
    Route::get('/edit-partner/{id}', 'Admin\PartnerController@editPartner');
    Route::post('/update-partner-data', 'Admin\PartnerController@updatePartnerData');
    Route::post('/change-status-partner', 'Admin\PartnerController@changeStatusPartner');

    //partner
    Route::get('/list-slots', 'Admin\SlotsController@listSlots');
    Route::get('/add-slot', 'Admin\SlotsController@addSlots');
    Route::post('/add-slot', 'Admin\SlotsController@addSlotsData');
    Route::get('/edit-slot/{id}', 'Admin\SlotsController@addSlots');
    Route::post('/update-slot/{id}', 'Admin\SlotsController@addSlotsData');
    Route::post('/change-status-slot', 'Admin\SlotsController@changeStatusSlot');

    //partner sales dashboard
    Route::get('/partner-sales-dashboard/{id}', 'Admin\PartnerSalesDetailsController@salesDashboard');
    Route::get('/partner-sales-details/{id}/{slot_id?}', 'Admin\PartnerSalesDetailsController@salesDetails');

});


/* Admin routes */
Route::group(['prefix' => 'partner'], function () {
    Route::get('/', function () {
        return redirect('/partner/login');
    });
    Route::get('/login', 'Partner\HomeController@loginCheck');
    Route::post('/login', 'Partner\HomeController@login');
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    });

    Route::post('/admin-send-link', 'Partner\HomeController@adminSendLink');
    Route::get('/admin-reset-password/{remember_token}', 'Partner\HomeController@adminredirectLink');
    Route::post('/admin-reset-password/{remember_token}', 'Partner\HomeController@adminresetPassword');

    Route::get('home', 'Partner\HomeController@index');

    Route::get('logout', 'Partner\HomeController@logout');

    Route::get('/site-settings', 'Partner\HomeController@siteSettings');
    Route::post('/update-site-settings', 'Partner\HomeController@updateSiteSettings');

    Route::get("/change-password", function () {
        return View::make("partner.changePassword");
    })->middleware('partnerauth');
    Route::post('/change-password', 'Partner\HomeController@changePassword');


});
