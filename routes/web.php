<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Site\CoursesController;
use App\Http\Controllers\Site\ContactController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [SiteController::class, 'index'])->name('Home');
Route::get('/about', [SiteController::class, 'about'])->name('About');
Route::get('/about-detail', [SiteController::class, 'aboutDetail'])->name('AboutDetail');
//contact route
Route::get('/contact', [ContactController::class, 'contact'])->name('Contact');
Route::post('/contact', [ContactController::class, 'contactSubmit'])->name('ContactSubmit');
//contact route end

//newsletter route
Route::post('/newsletter', [ContactController::class, 'Newsletter'])->name('Newsletter');
//newsletter route end
//join now route
Route::get('/join-now', [ContactController::class, 'joinNow'])->name('JoinNow');
Route::post('/join-now', [ContactController::class, 'joinNowSubmit'])->name('JoinNowSubmit');
//join now route end
//courses  route
Route::get('/courses-catagory/{slug}', [CoursesController::class, 'courseCatagory'])->name('courseCatagory');
Route::get('/courses', [CoursesController::class, 'courses'])->name('Courses');
Route::get('/courses-all', [CoursesController::class, 'coursesAll'])->name('CoursesAll');
Route::get('/courses/{slug}', [CoursesController::class, 'coursesDetail'])->name('CoursesDetail');
//courses route end
//faq route
Route::get('/faq', [SiteController::class, 'faq'])->name('FAQ');
//faq route end