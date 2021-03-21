<?php

use Tritiyo\Site\Controllers\SiteController;
use Illuminate\Http\Request;
Route::group(['middleware' => ['web','role:1,3,8']], function () {
    Route::any('sites/search', [SiteController::class, 'search'])->name('sites.search');

    Route::resources([
        'sites' => SiteController::class,
    ]);



    //Task Status Complete
    Route::any('site/updated-status', function(Request $request){

        //When request From Site_status_update.blade
        if(isset($request->batch_status_update)){
            foreach($request->batch_status_update as $key => $v){
                if(array_key_exists('site_id', $v)){
                    $data = new Tritiyo\Site\Models\TaskSiteComplete();
                    $data->user_id = $v['user_id'];
                    $data->task_id = $v['task_id'];
                    $data->task_for =$v['task_for'];
                    $data->site_id = $v['site_id'];
                    $data->status = 'Completed';
                    $data->save();
                    Tritiyo\Site\Models\Site::where('id',  $v['site_id'])->update(['completion_status' => 'Completed']);
                }
            }

            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        }


        //When Request from Show.balde.php
        if(!empty($request->show_page_single_site_id)){
            Tritiyo\Site\Models\Site::where('id',  $request->show_page_single_site_id)->update(['completion_status' => 'Completed']);
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        }


        return view('site::site_status_update');
    })->name('site.status.update');


});
