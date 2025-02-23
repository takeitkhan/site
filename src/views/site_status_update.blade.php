@extends('layouts.noapp')

@section('title')
    Sites
@endsection
@section('full_width_content')

    <?php
        $todayDate = date('Y-m-d');
        $last2DaysDate = date('Y-m-d', strtotime($todayDate. ' - 1 days'));
        $manager_id = auth()->user()->id;

        //dd($last2DaysDate);
        $sites = Tritiyo\Task\Models\Task::leftjoin('tasks_site', 'tasks_site.task_id', 'tasks.id')
                                    ->leftjoin('sites', 'sites.id', 'tasks_site.site_id')
                                    ->select('tasks.id as task_id', 'tasks.task_for as task_for', 'tasks_site.site_id as site_id', 'sites.*')
                                    ->where('tasks.user_id', $manager_id)
                                    ->where('sites.completion_status', 'Running')
                                    ->where(function ($query) {
                                        $query->where('sites.site_type',  NULL)
                                              ->orWhere('sites.site_type',  'Fresh')
                                              ->orWhere('sites.site_type',  'Old');
                                    })
                                    //->where('tasks.task_for', [$last2DaysDate, $todayDate])
                                    ->where('tasks.task_for', '<', $last2DaysDate)
                                    ->groupBy('tasks_site.site_id')
                                    ->get();
        //dd($sites);
    ?>
                                      
                                      
    @if(count($sites) > 0)
        <div class="columns mt-5">
            <div class="column is-10 mx-auto">
                <article class="message is-warning">
                    <div class="message-body">
                        আপনি এই প্যানেল থেকে সাইটের কাজ শেষ হয়েছে কিনা সেই স্ট্যাটাস হালনাগাদ করতে পারবেন। এখান থেকে
                        আপনি প্রথমে সাইট গুলোর পাশে থাকা চেকবক্স চেক করে সাবমিট এজ কমপ্লিট বাটন চাপ দিয়েই স্ট্যাটাস
                        হালনাগাদ করতে পারবেন। তবে যদি দেখুন প্রতিটি সাইট এখনো রানিং অবস্থায় আছে সেক্ষেত্রে সিলেক্ট অল
                        চেক দিয়ে সাবমিট এজ রানিং দিয়ে দেয়ার মাধ্যমে সাইটের স্ট্যাটাস হালনাগাদ করতে পারবেন।
                    </div>
                </article>


                <form action="{{route('site.status.update')}}" method="post">
                    @csrf
                    <div class="is-inline-flex">
                        <div class="field is-inline">
                            <label for="select_all" class="button is-small ml-2">
                                <input type="checkbox" id="select_all" class="mr-1"> Select All
                            </label>
                        </div>
                        
                        <div class="field is-inline">
                            <div class="control">
                                <div class="select is-small">
                                    <select id="status" name="status">
                                        <option value="Running">Running</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field is-inline">
                            <div class="control">
                                <button class="button is-small is-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                    <div class="columns is-multiline mt-3">
                        @foreach($sites as $key => $site)
                            <div class="column is-2">
                                <div class="borderedCol has-background--light">
                                    <label class="checkbox"
                                           style="display: block; -webkit-box-align: center;-ms-flex-align: center; align-items: center;">
                                        <div class="columns">
                                            <div class="column is-2">
                                                <input type="hidden" value="{{$manager_id}}"
                                                       name="batch_status_update[{{$key}}][user_id]">
                                                <input type="hidden" value="{{$site->task_id}}"
                                                       name="batch_status_update[{{$key}}][task_id]">
                                                <input type="hidden" value="{{$site->task_for}}"
                                                       name="batch_status_update[{{$key}}][task_for]">
                                                <input type="checkbox" value="{{$site->site_id}}"
                                                       class="status_update_all"
                                                       name="batch_status_update[{{$key}}][site_id]">
                                            </div>
                                            <div class="column is-10">
                                                <strong>Site Code: </strong>
                                                {{ $site->site_code }}
                                                <br/>
                                                <strong>Location: </strong>
                                                {{ $site->location }}
                                                <br/>

                                                <strong>Status: </strong>
                                                {{ $site->completion_status }}
                                                <br/>
                                                <strong>Project: </strong>
                                                @php
                                                    $project = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()
                                                @endphp
                                                {{  $project->name }}
                                                <br/>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    
                    <!--<button type="submit" class="button is-link is-small mt-3">Submit</button>-->
                </form>
            </div>
        </div>
    @else
        <?php echo redirect()->route('dashboard'); ?>
    @endif

    <?php  //dump($t); ?>
    <script>
        document.getElementById('select_all').onclick = function () {
            var checkboxes = document.getElementsByClassName('status_update_all');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>

@endsection
                                      
                                      
@section('cusjs')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script>   
    // $('form').on('submit', function (e) {
    //     var status = $('#status').val();
    //     if (status) {
    //         var message = status === 'Completed'
    //             ? 'Are you sure your selected sites have been completed?'
    //             : 'Are you sure your selected sites are still running?';
    
    //         if (!confirm(message)) {
    //             e.preventDefault(); // Prevent form submission if user cancels
    //         }
    //     } else {
    //         alert('Please select a status');
    //         e.preventDefault(); // Prevent form submission if status is not selected
    //     }
    // });
</script>
@endsection
