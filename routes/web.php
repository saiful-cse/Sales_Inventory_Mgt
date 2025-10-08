<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Middleware\SessionAuthenticate;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Middleware\TokenVerificationMiddleware;

Route::get('/', [HomeController::class, 'Home'])->name('home');

Route::post('/user_registration', [UserController::class, 'userRegistration'])->name('userRegistration');
Route::post('/user_login', [UserController::class, 'UserLogin'])->name('UserLogin');

//Route::get('/user_logout', [UserController::class, 'UserLogout'])->name('UserLogout');
Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->middleware([SessionAuthenticate::class])->name('Dashboard');

Route::post('/send-otp', [UserController::class, 'SendOtpCode'])->name('send_otp');
Route::post('/verify_otp', [UserController::class, 'VerifyOtp'])->name('verify_otp');

Route::post('/reset_password', [UserController::class, 'ResetPassword'])->middleware([SessionAuthenticate::class]);

Route::middleware(SessionAuthenticate::class)->group(function () {
    //Category all routes
    Route::controller(CategoryController::class)->group(function () {
        Route::post('/category_create', [CategoryController::class, 'CategoryCreate'])->name('category_create');
        Route::get('/category_list', [CategoryController::class, 'CategoryList'])->name('category_list');
        Route::get('/category_by_id', [CategoryController::class, 'CategoryById'])->name('category_by_id');
        Route::post('/category_update', [CategoryController::class, 'CategoryUpdate'])->name('category_update');
        Route::get('/category_delete/{id}', [CategoryController::class, 'CategoryDelete'])->name('category_delete');
        Route::get('/category_page', [CategoryController::class, 'CategoryPage'])->name('category.page');
        Route::get('/category_save_page', [CategoryController::class, 'CategorySavePage'])->name('categorySavePage');
    });

    //Product all routes
    Route::controller(ProductController::class)->group(function () {
        Route::post('/product_create', [ProductController::class, 'productCreate'])->name('product_create');
        Route::post('/product_list', [ProductController::class, 'productList'])->name('product_list');
        Route::post('/product_by_id', [ProductController::class, 'productById'])->name('product_by_id');
        Route::post('/product_update', [ProductController::class, 'productUpdate'])->name('product_update');
        Route::get('/product_delete/{id}', [ProductController::class, 'productDelete'])->name('product_delete');
        Route::get('/product_page', [ProductController::class, 'ProductPage'])->name('product_page');
        Route::get('/product_save_page', [ProductController::class, 'ProductSavePage'])->name('product_save_page');
    });

    //customer all routes
    Route::controller(CustomerController::class)->group(function () {
        Route::post('/customer_create', [CustomerController::class, 'customerCreate'])->name('customer.create');
        Route::post('/customer_list', [CustomerController::class, 'customerList'])->name('customer.list');
        Route::post('/customer_by_id', [CustomerController::class, 'customerById'])->name('customer.by_id');
        Route::post('/customer_update', [CustomerController::class, 'customerUpdate'])->name('customer.update');
        Route::get('/customer_delete/{id}', [CustomerController::class, 'customerDelete'])->name('customer.delete');
        Route::get('/customer_page', [CustomerController::class, 'CustomerPage'])->name('customer_page');
        Route::get('/customer_save_page', [CustomerController::class, 'CustomerSavePage'])->name('customer_save_page');
    });

    //invoice all routes
    Route::controller(InvoiceController::class)->group(function () {
        Route::post('/invoice_create', [InvoiceController::class, 'invoiceCreate'])->name('invoice.create');
        Route::get('/invoice_list', [InvoiceController::class, 'invoiceList'])->name('invoice.list');
        Route::post('/invoice_details', [InvoiceController::class, 'invoiceDetails'])->name('invoice.list');
        Route::get('/invoice_delete/{id}', [InvoiceController::class, 'invoiceDelete'])->name('invoice.delete');
        Route::get('/invoice_list_page', [InvoiceController::class, 'InvoiceListPage'])->name('invoice_list_page');
    });

    //dashboard all routes
    Route::get('/dashboard_summary', [DashboardController::class, 'DashboardSummary'])->name('dashboard.summary');

    Route::post('/user_update', [UserController::class, 'UserUpdate'])->name('user.update');

    Route::get('/create_sale', [SaleController::class, 'SalePage'])->name('create_sale');
    Route::get('/profile_page', [UserController::class, 'ProfilePage'])->name('profile_page');
    Route::get('/user_logout', [UserController::class, 'UserLogout'])->name('UserLogout');

    
});

//Frontend all routes
Route::get('/login', [UserController::class, 'LoginPage'])->name('loginPage');
Route::get('/registration', [UserController::class, 'RegistrationPage'])->name('registration');
Route::get('/send_otp', [UserController::class, 'SendOtpPage'])->name('send_otp');
Route::get('/verify_otp', [UserController::class, 'VerifyOtpPage'])->name('verify_otp');
Route::get('/reset_password', [UserController::class, 'ResetPasswordPage'])->middleware([SessionAuthenticate::class])->name('reset_password_page');
