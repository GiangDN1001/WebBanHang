<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/'); 
})->name('logout');

// ====== Home & Pages ======
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/about', [HomeController::class, 'about'])->name('home.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('home.contact');
Route::post('/contact/store', [HomeController::class, 'contact_store'])->name('home.contact.store');
Route::get('/search', [HomeController::class, 'search'])->name('home.search');
Route::get('/blog/{slug}', [HomeController::class, 'blog_detail'])->name('blog.detail');

// ====== Shop ======
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');

// ====== Cart ======
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');

// ====== Wishlist ======
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::delete('/wishlist/remove/{rowId}', [WishlistController::class, 'remove_item_from_wishlist'])->name('wishlist.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.empty');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');

// ====== User Account (Auth Protected) ======
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');

    // Cart - Extra quantity
    Route::put('/cart/increase-qunatity/{rowId}', [CartController::class, 'increase_item_quantity'])->name('cart.increase.qty');
    Route::put('/cart/reduce-qunatity/{rowId}', [CartController::class, 'reduce_item_quantity'])->name('cart.reduce.qty');

    // Checkout
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/place-order', [CartController::class, 'place_order'])->name('cart.place.order');
    Route::get('/order-confirmation', [CartController::class, 'confirmation'])->name('cart.confirmation');

    // Order
    Route::get('/account-orders', [UserController::class, 'account_orders'])->name('user.account.orders');
    Route::get('/account-order-detials/{order_id}', [UserController::class, 'account_order_details'])->name('user.acccount.order.details');
    Route::put('/account-order/cancel-order', [UserController::class, 'account_cancel_order'])->name('user.account_cancel_order');

    // Coupon
    Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');
    Route::delete('/cart/remove-coupon', [CartController::class, 'remove_coupon_code'])->name('cart.coupon.remove');

    // Address
    Route::get('/user/address', [UserController::class, 'address'])->name('user.address');
    Route::get('/admin/address/add', [UserController::class, 'address_add'])->name('user.address.add');
    Route::post('/admin/address/store', [UserController::class, 'address_store'])->name('user.address.store');
    Route::get('/address/edit/{id}', [UserController::class, 'address_edit'])->name('user.address.edit');
    Route::post('/address/update/{id}', [UserController::class, 'address_update'])->name('user.address.update');
});

// ====== Admin Panel (Auth + Admin middleware) ======
Route::middleware(['auth', AuthAdmin::class])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    // Coupons
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/coupon/add', [AdminController::class, 'add_coupon'])->name('admin.coupon.add');
    Route::post('/coupon/store', [AdminController::class, 'add_coupon_store'])->name('admin.coupon.store');
    Route::get('/coupon/edit/{id}', [AdminController::class, 'edit_coupon'])->name('admin.coupon.edit');
    Route::put('/coupon/update', [AdminController::class, 'update_coupon'])->name('admin.coupon.update');
    Route::delete('/coupon/{id}/delete', [AdminController::class, 'delete_coupon'])->name('admin.coupon.delete');

    // Brands
    Route::get('/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/brand/add', [AdminController::class, 'add_brand'])->name('admin.brand.add');
    Route::post('/brand/store', [AdminController::class, 'brand_store'])->name('admin.brand.store');
    Route::get('/brand/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brand.edit');
    Route::put('/brand/update', [AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('/brand/{id}/delete', [AdminController::class, 'brand_delete'])->name('admin.brand.delete');

    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/category/add', [AdminController::class, 'category_add'])->name('admin.category.add');
    Route::post('/category/store', [AdminController::class, 'category_store'])->name('admin.category.store');
    Route::get('/category/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.category.edit');
    Route::put('/category/update', [AdminController::class, 'category_update'])->name('admin.category.update');
    Route::delete('/category/{id}/delete', [AdminController::class, 'category_delete'])->name('admin.category.delete');

    // Products
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::post('/product/store', [AdminController::class, 'product_store'])->name('admin.product.store');
    Route::get('/product/{id}/edit', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/product/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/product/{id}/delete', [AdminController::class, 'product_delete'])->name('admin.product.delete');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/order/items/{order_id}', [AdminController::class, 'order_items'])->name('admin.order.items');
    Route::put('/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');

    // Slides
    Route::get('/slides', [AdminController::class, 'slides'])->name('admin.slides');
    Route::get('/slide/add', [AdminController::class, 'slide_add'])->name('admin.slide.add');
    Route::post('/slide/store', [AdminController::class, 'slide_store'])->name('admin.slides.store');
    Route::get('/slide/{id}/edit', [AdminController::class, 'slide_edit'])->name('admin.slide.edit');
    Route::put('/slide/update', [AdminController::class, 'slide_update'])->name('admin.slide.update');
    Route::delete('/slide/{id}/delete', [AdminController::class, 'slide_delete'])->name('admin.slide.delete');

    // Contacts
    Route::get('/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::delete('/contact/{id}/delete', [AdminController::class, 'contact_delete'])->name('admin.contact.delete');

    // Settings
    Route::get('/settings', [AdminController::class, 'showSettings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateProfile'])->name('admin.settings.update');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');

    // Search
    Route::get('/search', [AdminController::class, 'search'])->name('admin.search');

    // Blogs
    Route::get('/blogs', [AdminController::class, 'blogs'])->name('admin.blogs');
    Route::get('/blogs/create', [AdminController::class, 'blogs_create'])->name('admin.blogs.create');
    Route::post('/blogs/store', [AdminController::class, 'blog_store'])->name('admin.blogs.store');
    Route::get('/blogs/edit/{id}', [AdminController::class, 'blog_edit'])->name('admin.blogs.edit');
    Route::put('/blogs/update/{id}', [AdminController::class, 'blog_update'])->name('admin.blogs.update');
    Route::delete('/blogs/delete/{id}', [AdminController::class, 'blog_delete'])->name('admin.blogs.delete');
});
