<?php
Route::post('upload/single', 'Api\UploadController@uploadSingleFile');
Route::post('upload/multiple', 'Api\UploadController@uploadMultipleFiles');
