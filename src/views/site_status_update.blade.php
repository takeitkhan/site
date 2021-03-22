@extends('layouts.app')

@section('title')
    Sites
@endsection
@section('column_left')

    @php
            $manager_id = auth()->user()->id;
/*
            $sites = \DB::table('sites')->leftJoin('projects', 'projects.id', 'sites.project_id')
                            ->select('sites.*', 'projects.manager')
                            ->where('projects.manager', $manager_id)
                            ->where('sites.completion_status', 'Running')
                            ->groupBy('sites.project_id')
                            ->groupBy('sites.id')
                            ->get();
*/
            $sites = Tritiyo\Task\Models\Task::leftjoin('tasks_site', 'tasks_site.task_id', 'tasks.id')
                                        ->leftjoin('sites', 'sites.id', 'tasks_site.site_id')
                                        ->select('tasks.id as task_id', 'tasks.task_for as task_for', 'tasks_site.site_id as site_id', 'sites.site_code as site_code', 'sites.completion_status as completion_status')
                                        ->where('tasks.user_id', $manager_id)
                                        ->where('sites.completion_status', 'Running')
                                        ->groupBy('tasks_site.site_id')
                                        ->get();

    @endphp

    <form action="{{route('site.status.update')}}" method="post">
        @csrf
        <div class="columns is-multiline mt-3">
            @foreach($sites as $key => $site)
                <div class="column is-2">
                    <div class="borderedCol has-background--light">
                        <label class="checkbox">

                            <input type="hidden" value="{{$manager_id}}" name="batch_status_update[{{$key}}][user_id]">
                            <input type="hidden" value="{{$site->task_id}}" name="batch_status_update[{{$key}}][task_id]">
                            <input type="hidden" value="{{$site->task_for}}" name="batch_status_update[{{$key}}][task_for]">
                            <input type="checkbox" value="{{$site->site_id}}" class="status_update_all" name="batch_status_update[{{$key}}][site_id]">

                            <span class="has-text-primary-dark">{{$site->site_code}}</span> <br/>
                            <small class="ml-4">{{$site->completion_status}}</small>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        <label for="select_all" class="button is-small is-warning ml-2">
            <input type="checkbox" id="select_all" class="mr-1">  Select All
        </label>
        <button type="submit" class="button is-link is-small">Submit as completed</button>
        <button class="button is-link is-light is-small">Submit as Running</button>
    </form>

    <?php  //dump($t); ?>
    <script>
        document.getElementById('select_all').onclick = function() {
            var checkboxes = document.getElementsByClassName('status_update_all');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>

@endsection

