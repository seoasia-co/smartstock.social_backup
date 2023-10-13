<?php

Route::get('/', 'APIsController@api')->name('apiURL');
// general
Route::get('/website/status', 'APIsController@website_status');
Route::get('/website/info/{lang?}', 'APIsController@website_info');
Route::get('/website/contacts/{lang?}', 'APIsController@website_contacts');
Route::get('/website/style/{lang?}', 'APIsController@website_style');
Route::get('/website/social', 'APIsController@website_social');
Route::get('/website/settings', 'APIsController@website_settings');
Route::get('/menu/{menu_id}/{lang?}', 'APIsController@menu');
Route::get('/banners/{group_id}/{lang?}', 'APIsController@banners');
// section & topics
Route::get('/section/{section_id}/{lang?}', 'APIsController@section');
Route::get('/categories/{section_id}/{lang?}', 'APIsController@categories');
Route::get('/topics/{section_id}/page/{page_number?}/count/{topics_count?}/{lang?}', 'APIsController@topics');
Route::get('/category/{cat_id}/page/{page_number?}/count/{topics_count?}/{lang?}', 'APIsController@category');
// topic sub details
Route::get('/topic/fields/{topic_id}/{lang?}', 'APIsController@topic_fields');
Route::get('/topic/photos/{topic_id}/{lang?}', 'APIsController@topic_photos');
Route::get('/topic/photo/{photo_id}/{lang?}', 'APIsController@topic_photo');
Route::get('/topic/maps/{topic_id}/{lang?}', 'APIsController@topic_maps');
Route::get('/topic/map/{map_id}/{lang?}', 'APIsController@topic_map');
Route::get('/topic/files/{topic_id}/{lang?}', 'APIsController@topic_files');
Route::get('/topic/file/{file_id}/{lang?}', 'APIsController@topic_file');
Route::get('/topic/comments/{topic_id}/{lang?}', 'APIsController@topic_comments');
Route::get('/topic/comment/{comment_id}/{lang?}', 'APIsController@topic_comment');
Route::get('/topic/related/{topic_id}/{lang?}', 'APIsController@topic_related');
// topic page
Route::get('/topic/{topic_id}/{lang?}', 'APIsController@topic');
// user topics
Route::get('/user/{user_id}/topics/page/{page_number?}/count/{topics_count?}/{lang?}', 'APIsController@user_topics');
// Forms Submit
Route::post('/subscribe', 'APIsController@subscribeSubmit');
Route::post('/comment', 'APIsController@commentSubmit');
Route::post('/order', 'APIsController@orderSubmit');
Route::post('/contact', 'APIsController@ContactPageSubmit');

// SMAI Sync data
Route::post('/smaisync/tokens', 'APIsController@smaisync_tokens');
Route::post('/smaicheck/column', 'APIsController@smaicheck_column');
Route::post('/smaiupdate/column', 'APIsController@smaiupdate_column');
Route::post('/smaisync/maintokens', 'APIsController@smaisync_main_tokens');
Route::post('/smaisync/translate', 'APIsController@smai_translation');
Route::post('/smaisync/textgen', 'APIsController@smai_text_gen');



// update profile     
Route::post('/smaiupdate/profile', 'APIsController@smaiuser_update_profile');

// Sign Up & Auth
Route::post('/smainewuser/createallfreetrial', 'APIsController@smainewuser_createallfreetrial');

// Plans
Route::post('/smaicheck/plans', 'APIsController@smaicheck_plans');
Route::post('/smaiupdate/plan', 'APIsController@smaiuser_update_plan');

//SEO from smartcontent.co.in/seo
Route::get('/manage-cron-all-posts/{id}', 'APIsController@smai_seo_manage_cron_all_posts');
Route::post('/smai-seo/usercreatepost', 'APIsController@smai_seo_user_create_cron_posts');
  


// EOF SMAI Sync data

