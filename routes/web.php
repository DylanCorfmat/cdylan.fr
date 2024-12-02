<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;
use App\Http\Controllers\Front\{
    PostController as FrontPostController,
    CommentController as FrontCommentController,
    ContactController as FrontContactController,
    PageController as FrontPageController
};
use App\Http\Controllers\Back\{
    AdminController,
    PostController as BackPostController,
    UserController as BackUserController,
    ResourceController as BackResourceController
};
use App\Http\Controllers\Auth\RegisteredUserController;

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

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => 'auth'], function () {
    Lfm::routes();
});

Route::name('home')->get('/', [FrontPostController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'password.confirm')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::view('profile', 'auth.profile');
    Route::name('profile')->put('profile', [RegisteredUserController::class, 'update']);
    Route::name('deleteAccount')->delete('profile/delete',  [RegisteredUserController::class, 'destroy']);
});

Route::prefix('posts')->group(function () {
    Route::name('posts.display')->get('{slug}', [FrontPostController::class, 'show']);
    Route::name('posts.search')->get('', [FrontPostController::class, 'search']);
    Route::name('posts.comments')->get('{post}/comments', [FrontCommentController::class, 'comments']);
    Route::name('posts.comments.store')->post('{post}/comments', [FrontCommentController::class, 'store'])->middleware('auth');
});
Route::name('front.comments.destroy')->delete('comments/{comment}', [FrontCommentController::class, 'destroy']);
Route::name('category')->get('category/{category:slug}', [FrontPostController::class, 'category']);
Route::name('author')->get('author/{user}', [FrontPostController::class, 'user']);
Route::name('tag')->get('tag/{tag:slug}', [FrontPostController::class, 'tag']);
Route::resource('contacts', FrontContactController::class, ['only' => ['create', 'store']]);
Route::name('page')->get('page/{page:slug}', FrontPageController::class);

Route::view('admin', 'back.layout');

Route::prefix('admin')->group(function () {
    Route::middleware('redac')->group(function () {
        Route::name('admin')->get('/', [AdminController::class, 'index']);
        Route::name('purge')->put('purge/{model}', [AdminController::class, 'purge']);
        Route::resource('posts', BackPostController::class)->except(['show', 'create']);
        Route::name('posts.create')->get('posts/create/{id?}', [BackPostController::class, 'create']);
        Route::name('users.valid')->put('valid/{user}', [BackUserController::class, 'valid']);
        Route::name('users.unvalid')->put('unvalid/{user}', [BackUserController::class, 'unvalid']);
        Route::resource('comments', BackResourceController::class)->except(['show', 'create', 'store']);
        Route::name('comments.indexnew')->get('newcomments', [BackResourceController::class, 'index']);
    });

    Route::middleware('admin')->group(function () {
        Route::name('posts.indexnew')->get('newposts', [BackPostController::class, 'index']);
        Route::get('/posts/all', [BackPostController::class, 'newindex'])->name('indexPosts');
        Route::get('/posts/drafts', [BackPostController::class, 'drafts'])->name('draftsPosts');
        Route::get('/posts/scheduled', [BackPostController::class, 'scheduled'])->name('scheduledPosts');
        Route::get('/posts/published', [BackPostController::class, 'published'])->name('publishedPosts');
        Route::get('/posts/archived', [BackPostController::class, 'archived'])->name('archivedPosts');
        Route::resource('categories', BackResourceController::class)->except(['show']);
        Route::resource('users', BackUserController::class)->except(['show', 'create', 'store']);
        Route::name('users.indexnew')->get('newusers', [BackResourceController::class, 'index']);
        Route::resource('contacts', BackResourceController::class)->only(['index', 'destroy']);
        Route::name('contacts.indexnew')->get('newcontacts', [BackResourceController::class, 'index']);
        Route::resource('follows', BackResourceController::class)->except(['show']);
        Route::resource('pages', BackResourceController::class)->except(['show']);
    });
});



require __DIR__.'/auth.php';
