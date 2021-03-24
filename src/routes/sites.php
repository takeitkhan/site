<?php

use Tritiyo\Site\Controllers\SiteController;
use Illuminate\Http\Request;

Route::group(['middleware' => ['web', 'role:1,3,4,8']], function () {
    Route::any('sites/search', [SiteController::class, 'search'])->name('sites.search');

    Route::resources([
        'sites' => SiteController::class,
    ]);


    //Task Status Complete
    Route::any('site/updated-status', function (Request $request) {

        //When request From Site_status_update.blade
        if (isset($request->batch_status_update)) {
            //dd($request->batch_status_update);
            if ($request->status_running) {
                $status = 'Running';
            } else {
                $status = 'Completed';
            }
            //dd($status);

            $html = '<table border="1" width="100%" style="border-collapse:collapse">';
            $html .= '<tr align="center">';
            $html .= '<td><strong>Updated By</strong></td>';
            $html .= '<td><strong>Site code</strong></td>';
            $html .= '<td><strong>Completions Status</strong></td>';
            $html .= '</tr>';

            $totalArray = array_sum(array_count_values(array_column($request->batch_status_update, 'site_id')));
            $i = 0;

            foreach ($request->batch_status_update as $key => $v) {
                if (array_key_exists('site_id', $v)) {
                    $data = new Tritiyo\Site\Models\TaskSiteComplete();
                    $data->user_id = $v['user_id'];
                    $data->task_id = $v['task_id'];
                    $data->task_for = $v['task_for'];
                    $data->site_id = $v['site_id'];
                    $data->status = $status;
                    $data->save();
                    Tritiyo\Site\Models\Site::where('id', $v['site_id'])->update(['completion_status' => $status]);


                    $html .= '<tr align="center">';
                    $html .= '<td>' . App\Models\User::where('id', $v['user_id'])->first()->name . '</td>';
                    $html .= '<td>' . \Tritiyo\Site\Models\Site::where('id', $v['site_id'])->first()->site_code . '</td>';
                    $html .= '<td>' . $status . '</td>';
                    $html .= '</tr>';

                    $i++;
                }
            }


            //dd($html);
            //dump($i);
            //dd();
            $emailAddress = auth()->user()->email;
            if( $status == 'Completed') {
                if ($i == $totalArray) {
                    //Send Mail
                    Tritiyo\Task\Helpers\MailHelper::send($html, 'Site Completion Status', $emailAddress);
                }
            }

            return redirect()->route('dashboard')->with(['status' => 1, 'message' => 'Successfully updated']);
        }


        //When Request from Show.balde.php
        if (!empty($request->show_page_single_site_id)) {
            //Tritiyo\Site\Models\Site::where('id',  $request->show_page_single_site_id)->update(['completion_status' => 'Completed']);
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        }


        return view('site::site_status_update');


    })->name('site.status.update');


});
