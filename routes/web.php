<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;
   
Route::get('/', function () {
    return view('welcome');
});
  
Auth::routes();
  
// Route::get('/admin', [AdminController::class, 'index'])->name('admin');
  
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/why', [HomeController::class, 'why'])->name('why');
Route::get('/', [HomeController::class, 'showHomePageSection'])->name('showHomePageSection');
  
Route::group(['middleware' => ['auth']], function() {

    Route::get('admin', [AdminController::class, 'index'])->name('admin.index');

    Route::get('admin-users', [AdminController::class, 'adminUsers'])->name('admin-users');
    Route::get('admin-create-user', [AdminController::class, 'adminCreateUser'])->name('admin-create-user');
    Route::post('admin-store-user', [AdminController::class, 'adminStoreUser'])->name('admin-store-user');
    Route::get('admin-edit-user/{id}', [AdminController::class, 'adminEditUser'])->name('admin-edit-user');
    Route::put('admin-update-user/{id}', [AdminController::class, 'adminUpdateUser'])->name('admin-update-user');
    Route::delete('admin-delete-user/{id}', [AdminController::class, 'adminDestroyUser'])->name('admin-delete-user');

    Route::get('admin-roles', [AdminController::class, 'adminRoles'])->name('admin-roles');
    Route::get('admin-create-role', [AdminController::class, 'adminCreateRole'])->name('admin-create-role');
    Route::post('admin-store-role', [AdminController::class, 'adminStoreRole'])->name('admin-store-role');
    Route::get('admin-edit-role/{id}', [AdminController::class, 'adminEditRole'])->name('admin-edit-role');
    Route::put('admin-update-role/{id}', [AdminController::class, 'adminUpdateRole'])->name('admin-update-role');
    Route::delete('admin-delete-role/{id}', [AdminController::class, 'adminDestroyRole'])->name('admin-delete-role');

    Route::get('admin-categories', [AdminController::class, 'adminCategories'])->name('admin-categories');
    Route::get('admin-create-category', [AdminController::class, 'adminCreateCategory'])->name('admin-create-category');
    Route::post('admin-store-category', [AdminController::class, 'adminStoreCategory'])->name('admin-store-category');
    Route::get('admin-edit-category/{id}', [AdminController::class, 'adminEditCategory'])->name('admin-edit-category');
    Route::put('admin-update-category/{id}', [AdminController::class, 'adminUpdateCategory'])->name('admin-update-category');
    Route::delete('admin-delete-category/{id}', [AdminController::class, 'adminDestroyCategory'])->name('admin-delete-category');

    Route::get('admin-products', [AdminController::class, 'adminProducts'])->name('admin-products');
    Route::get('admin-create-product', [AdminController::class, 'adminCreateProduct'])->name('admin-create-product');
    Route::post('admin-store-product', [AdminController::class, 'adminStoreProduct'])->name('admin-store-product');
    Route::get('admin-edit-product/{id}', [AdminController::class, 'adminEditProduct'])->name('admin-edit-product');
    Route::put('admin-update-product/{id}', [AdminController::class, 'adminUpdateproduct'])->name('admin-update-product');
    Route::delete('admin-delete-product/{id}', [AdminController::class, 'adminDestroyProduct'])->name('admin-delete-product');

    Route::get('admin-homepage-sections', [AdminController::class, 'adminHomepageSections'])->name('admin.homepage.sections');
    Route::post('admin-update-homepage-sections', [AdminController::class, 'adminUpdateHomepageSections'])->name('admin-update-homepage-sections');

    Route::get('admin-profile', [AdminController::class, 'adminProfile'])->name('admin-profile');
    Route::get('admin-profile-edit-user/{id}', [AdminController::class, 'adminEditProfileUser'])->name('admin-profile-edit-user');
    Route::put('admin-profile-update-user/{id}', [AdminController::class, 'adminProfileUpdateUser'])->name('admin-profile-update-user');

    Route::get('admin-notifications', [AdminController::class, 'adminNotifications'])->name('admin-notifications');
    
    Route::get('admin-countries', [AdminController::class, 'adminCountries'])->name('admin-countries');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('products', ProductController::class);

});
