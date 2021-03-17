@extends('layouts.app')

@section('title')
    Single Site
@endsection

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Single Site',
            'spSubTitle' => 'view a Site',
            'spShowTitleSet' => true
        ])

        @include('component.button_set', [
            'spShowButtonSet' => true,
            'spAddUrl' => null,
            'spAddUrl' => route('sites.create'),
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
            'spTitle' => 'Sites',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spPlaceholder' => 'Search sites...',
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>
@section('column_left')
    {{--    <article class="panel is-primary">--}}
    {{--        <div class="customContainer">--}}
    <div class="card tile is-child">
        <header class="card-header">
            <p class="card-header-title">
                <span class="icon"><i class="fas fa-tasks default"></i></span>
                Main Site Data
            </p>
        </header>
        <div class="card-content">
            <div class="card-data">
                <div class="columns">
                    <div class="column is-3">Site Code</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $site->site_code }}</div>
                </div>
                <div class="columns">
                    <div class="column is-3">Project Name</div>
                    <div class="column is-1">:</div>
                    <div class="column">
                        <a href="{{ route('projects.show', $site->project_id) }}" target="_blank">
                            {{ \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()->name }}
                        </a>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-3">Location</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $site->location }}</div>
                </div>
                <div class="columns">
                    <div class="column is-3">Budget <small>[Project Budget divided by total sites]</small></div>
                    <div class="column is-1">:</div>
                    <div class="column">
                        @php
                            $total_project_budget = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()->budget;
                            $total_sites = \Tritiyo\Site\Models\Site::where('project_id', '=', $site->project_id)->get()->count();
                            echo $total_project_budget/$total_sites;
                        @endphp
                        {{-- {{ $site->budget }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $moves = \Tritiyo\Task\Models\TaskSite::where('site_id', $site->id)->groupBy('site_id')->get();
    @endphp
    @if($moves->count() > 0)
        <div class="card tile is-child" style="margin-top: 15px !important;">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-tasks default"></i></span>
                    Movement history
                </p>
            </header>
            <div class="card-content">
                <div class="card-data">
                    <table class="table is-bordered is-striped is-narrow is-hoverable">
                        <tr>
                            <th title="Task date">Task ID</th>
                            <th title="Task date">Task date</th>
                            <th title="Task head">Site Head</th>
                            <th title="Task description">Task Description</th>
                            <th title="Resource submitted bill amount">Other Resources</th>
                            <th title="Manager asked amount">Submitted Req.</th>
                            <th title="Accountant given amount">Approved Req.</th>
                            <th title="Resource submitted bill amount">Submitted Bill</th>
                            <th title="Resource submitted bill amount">Approved Bill</th>
                        </tr>
                        @foreach($moves as $move)
                            <tr>
                                <td title="Task ID">
                                    <a href="{{ route('tasks.show', $move->task_id) }}" target="_blank">
                                        {{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->task_name }}
                                        ({{ $move->task_id }})
                                    </a>
                                </td>
                                <td title="Task date">
                                    {{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->task_for }}
                                </td>
                                <td title="Task head">
                                    {{ \App\Models\User::where('id', \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->site_head)->first()->name }}
                                    ({{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->site_head }})
                                </td>
                                <td title="Task description">
                                    {{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->task_details }}
                                </td>
                                <td title="Resource Used">
                                    @php
                                        $rids = \DB::select('SELECT GROUP_CONCAT(resource_id) AS rids FROM `tasks_site` WHERE site_id = '. $move->site_id .' GROUP BY site_id');
                                        $resources = explode(',', $rids[0]->rids);
                                    @endphp
                                    @foreach($resources as $r)
                                        {{ \App\Models\User::where('id', $r)->first()->name }}<br/>
                                    @endforeach
                                </td>
                                <td title="Manager asked amount">
                                    <?php
                                    $manager_submit_req = new Tritiyo\Task\Helpers\SiteHeadTotal('requisition_prepared_by_manager', $move->task_id, true);
                                    echo $manager_submit_req->getTotal();
                                    ?>

                                </td>
                                <td title="Accountant given amount">
                                    <?php
                                        $accountant_approve_req = new Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $move->task_id, true);
                                        echo $accountant_approve_req->getTotal();
                                    ?>
                                </td>
                                <td title="Resource submitted bill amount">
                                    <?php
                                        $resource_submit_bill = new Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $move->task_id, true);
                                        echo $resource_submit_bill->getTotal();
                                    ?>
                                </td>
                                <td title="Accountant Approve bill amount">
                                    <?php
                                    $accountant_approve_bill = new Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $move->task_id, true);
                                        echo $accountant_approve_bill->getTotal();
                                    ?>
                                </td>

                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('column_right')

@endsection
@section('cusjs')
    <style type="text/css">
        .table.is-fullwidth {
            width: 100%;
            font-size: 15px;
            text-align: center;
        }
    </style>
@endsection
