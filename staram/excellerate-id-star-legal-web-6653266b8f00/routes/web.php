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
Route::get('/', 'DashboardController@root')->name('root');
Route::get('/home', 'DashboardController@home')->name('home');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/api/getTotalRequestInYear', 'DashboardController@_getTotalRequestInYear')->name('api.getTotalRequestInYear');
Route::post('/emailus', 'DashboardController@emailUs')->name('emailUs');

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::post('/changePassword','ProfileController@changePassword')->name('changePassword');
Route::post('/changeProfile','ProfileController@changeProfile')->name('changeProfile');
Route::post('/registerUser','RegisterUserController@registerUser')->name('registerUser');

Route::get('/request','RequestDocController@index' )->name('request');
Route::post('/submitRequest','RequestDocController@submitRequest')->name('submitRequest');
Route::post('/approveRequest','RequestDocController@approveRequest')->name('approveRequest');
Route::post('/activateRequest','RequestDocController@activateRequest')->name('activateRequest');
Route::post('/processRequest','RequestDocController@processRequest')->name('processRequest');
Route::get('/api/getRequestDocs', 'RequestDocController@_getRequestDocs')->name('api.getRequestDocs');
Route::get('/api/getRequestDetails/{requestId}/{mark?}', 'RequestDocController@_getRequestDetails')->name('api.getRequestDetails');
Route::get('/api/getRequestAttachment/{attachmentId}', 'RequestDocController@_getRequestAttachment')->name('api.getRequestAttachment');
Route::get('/api/getRequestDocs', 'RequestDocController@_getRequestDocs')->name('api.getRequestDocs');
Route::get('/api/getRequestedDocsCount', 'RequestDocController@_getRequestedDocsCount')->name('api.getRequestedDocsCount');
Route::get('/api/getLatestSubmission/{requestId}', 'RequestDocController@_getLatestSubmission')->name('api.getLatestSubmission');
Route::get('/getSubmissionAttachment/{requestId}/{submissionId?}','RequestDocController@_getSubmissionAttachment')->name('getSubmissionAttachment');
Route::post('/approveSubmission','RequestDocController@approveRequestSubmission')->name('approveRequestSubmission');
Route::post('/reviseRequest','RequestDocController@reviseRequest')->name('reviseRequest');

Route::get('/review', 'ReviewController@index')->name('review');
Route::post('/submitReview','ReviewController@submitReview')->name('submitReview');
Route::get('/api/getReviewDocs', 'ReviewController@_getReviewDocs')->name('api.getReviewDocs');

Route::get('/processed','GenericDocController@docProcessed')->name('docProcessed');
Route::get('/api/getProcessedDocs','GenericDocController@_getProcessedDocs')->name('getProcessedDocs');
Route::get('/complete', 'GenericDocController@complete')->name('complete');
Route::get('/api/getCompletedDocs','GenericDocController@_getCompletedDocs')->name('getCompletedDocs');
Route::get('/approved', 'GenericDocController@approved')->name('approved');
Route::get('/api/getApprovedDocs', 'GenericDocController@_getApprovedDocs')->name('getApprovedDocs');
Route::get('/hold', 'GenericDocController@hold')->name('hold');
Route::get('/api/getHoldDocs', 'GenericDocController@_getHoldDocs')->name('getHoldDocs');
Route::get('/tobeApproved', 'GenericDocController@tobeApproved')->name('tobeApproved');
Route::get('/api/getTobeApprovedDocs', 'GenericDocController@_getTobeApprovedDocs')->name('getTobeApprovedDocs');

Route::get('/share/{folderId?}/{phrase?}', 'SharedFileController@index')->name('share');
Route::post('/addFolder','SharedFileController@addFolder')->name('addFolder');
Route::post('/addFile','SharedFileController@addFile')->name('addFile');
Route::post('/doDocShare','SharedFileController@doDocShare')->name('doDocShare');
Route::post('/doDocDelete','SharedFileController@doDocDelete')->name('doDocDelete');
Route::get('/sharedFile/{fileName}','SharedFileController@sharedFile')->name('sharedFile');
Auth::routes();

Route::get('/availablePIC','UserController@index')->name('availablePIC');
Route::get('/userSetting','UserController@userSetting')->name('userSetting');
Route::get('/userall','UserController@getAllUser')->name('userAll');
Route::post('/chgRole','UserController@changeRole')->name('changeRole');

Route::get('/api/getSharedFolders/{folderId?}', 'SharedFileController@_getSharedFolders')->name('api.getSharedFolders');
Route::get('/api/getSharedDocs/{folderId?}', 'SharedFileController@_getSharedDocs')->name('api.getSharedDocs');
Route::get('/api/getSharedFoldersDocs/{folderId?}/{phrase?}', 'SharedFileController@_getSharedFoldersDocs')->name('api.getSharedFoldersDocs');
Route::get('/api/getDocPath/{folderId?}', 'SharedFileController@_getDocPath')->name('api.getDocPath');
Route::post('/api/getDownloadLinks', 'SharedFileController@_getDownloadLinks')->name('api.getDownloadLinks');
Route::get('/api/getDocType', 'SharedFileController@_getDocType')->name('api.getDocType');

Route::post('/api/getAuthorizeUser', 'UserController@_getAuthorizeUser')->name('api.getAuthorizeUser');
Route::post('/api/getUser', 'UserController@_getUser')->name('api.getUser');
Route::post('/api/getLegalPIC', 'UserController@_getLegalPIC')->name('api.getLegalPIC');