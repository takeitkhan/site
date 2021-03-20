@extends('layouts.app')

@section('title')
    Single Site
@endsection
@if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
    @php
        $addUrl = route('sites.create');
    @endphp
@else
    @php
        $addUrl = '#';
    @endphp
@endif
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
            'spAddUrl' => $addUrl,
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
            'spTitle' => 'Sites',
        ])

        @include('component.filter_set', [
            'spShowFilterSet' => true,
            'spAddUrl' => route('sites.create'),
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
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
        $moves = \Tritiyo\Task\Models\TaskSite::where('site_id', $site->id)->groupBy('task_id')->get();
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
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <tr>
                            <th title="Task date" width="15%">Task Name</th>
                            <th title="Task date" width="8%">Task date</th>
                            <th title="Task head">Site Head</th>
                            <th title="Task description" width="30%">Task Description</th>
                            <th title="Resources used">Resources used</th>                                                    
                            <th title="Accountant given amount">Approved Req.</th>                            
                            <th title="Accountant approved bill amount">Approved Bill</th>
                            <th title="Requisition - Bill">Outcome</th>
                        </tr>
                        @php
                            $in_total = [];
                        @endphp
                        
                        @foreach($moves as $move)
                            <tr>
                                <td title="Task ID">
                                    <a href="{{ route('tasks.show', $move->task_id) }}" target="_blank">
                                        {{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->task_name }}                                        
                                    </a>
                                </td>
                                <td title="Task date">
                                    {{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->task_for }}
                                </td>
                                <td title="Task head">
                                    {{ \App\Models\User::where('id', \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->site_head)->first()->name }}
                                    {{-- ({{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->site_head }}) --}}
                                </td>
                                <td title="Task description">
                                    {{ \Tritiyo\Task\Models\Task::where('id', $move->task_id)->first()->task_details }}
                                </td>
                                <td title="Resource Used">
                                    @php
                                        $rids = \DB::select('SELECT GROUP_CONCAT(resource_id) AS rids FROM `tasks_site` WHERE site_id = '. $move->site_id .' AND task_id = '. $move->task_id .' GROUP BY site_id');
                                        $resources = explode(',', $rids[0]->rids);
                                    @endphp
                                    @foreach($resources as $r)
                                        {{ \App\Models\User::where('id', $r)->first()->name }}<br/>
                                    @endforeach
                                </td>
                                @php
                                    # SELECT COUNT(site_id) AS total 
                                    # FROM `tasks_site` 
                                    # WHERE task_id = 16 
                                    # AND site_id = 24 
                                    # GROUP BY task_id, site_id

                                    $count_of_sites_of_this_tasks = \DB::select('SELECT COUNT(site_id) AS total FROM `tasks_site` WHERE task_id = '. $move->task_id .' AND site_id = '. $move->site_id .' GROUP BY task_id, site_id');
                                    $count = (int)$count_of_sites_of_this_tasks[0]->total;
                                    //dump($count_of_sites_of_this_tasks[0]->total)
                                @endphp
                                <td title="Accountant given amount">
                                    <?php
                                        $accountant_approved_requisition = new Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $move->task_id);                                        
                                        $requisition = $accountant_approved_requisition->getTotal() / $count ;
                                        echo 'BDT. ' . $requisition;
                                    ?>
                                </td>                                
                                <td title="Accountant approved bill amount">
                                    <?php
                                        $accountant_approve_bill = new Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $move->task_id);
                                        $bill = $accountant_approve_bill->getTotal() / $count;
                                        echo 'BDT. ' . $bill;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $accountant_approved_requisition_d = new Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $move->task_id);
                                        $accountant_approve_bill_d = new Tritiyo\Task\Helpers\SiteHeadTotal('bill_prepared_by_resource', $move->task_id);

                                        echo 'BDT. ' . $in_total[] = ($requisition - $bill);
                                    ?>
                                </td>
                            </tr>                            
                        @endforeach
                        <tr>
                            <td colspan="7">
                                In Total for this site
                            </td>
                            <td>
                                {{ 'BDT. ' . array_sum($in_total) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @php
        #$vehicle_used = \Tritiyo\Task\Models\TaskVehicle::where('site_id', $site->id)->get();
        $vehicle_used = \DB::select('SELECT task_id, (SELECT GROUP_CONCAT(vehicle_id) FROM tasks_vehicle WHERE tasks_vehicle.task_id = tasks_for_this_site.task_id) AS vehiclesss 
                                    FROM (SELECT task_id FROM `tasks_site` WHERE site_id = '. $site->id .' GROUP BY task_id) AS tasks_for_this_site');
        //dd($vehicle_used);
    @endphp
    @if(count($vehicle_used) > 0)
        <div class="card tile is-child" style="margin-top: 15px !important;">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-tasks default"></i></span>
                    Vehicle Used
                </p>
            </header>
            <div class="card-content">
                <div class="card-data">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <tr>
                            <th title="Task name"  width="15%">Task Name</th>
                            <th title="Task date" width="8%">Task date</th>                            
                            <th title="Task Vehicle Name">Vehicle Name</th>
                            {{-- <th title="Vehicle Rent">Vehicle Rent</th>
                            <th title="Note">Note</th> --}}
                        </tr>
                        @foreach($vehicle_used as $vehicle)
                            <tr>
                                <td title="Task ID">
                                    <a href="{{ route('tasks.show', $vehicle->task_id) }}" target="_blank">
                                        {{ \Tritiyo\Task\Models\Task::where('id', $vehicle->task_id)->first()->task_name }}                                        
                                    </a>
                                </td>
                                <td title="Task date">
                                    {{ \Tritiyo\Task\Models\Task::where('id', $vehicle->task_id)->first()->task_for }}
                                </td>
                                <td title="Task Vehicle Name">
                                    @php
                                        $vehicles = explode(',', $vehicle->vehiclesss)
                                    @endphp
                                    @foreach($vehicles as $v)
                                        {{ !empty($v) ? \Tritiyo\Vehicle\Models\Vehicle::where('id', $v)->first()->name : '' }}<br/>
                                    @endforeach
                                </td>

                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    @endif

    @php
        $material_used = \DB::select('SELECT task_id, (SELECT GROUP_CONCAT(material_id) FROM tasks_material WHERE tasks_material.task_id = tasks_for_this_site.task_id) AS materialsss 
                        FROM (SELECT task_id FROM `tasks_site` WHERE site_id = '. $site->id .' GROUP BY task_id) AS tasks_for_this_site');                        
    @endphp
    @if(count($material_used) > 0)
        <div class="card tile is-child" style="margin-top: 15px !important;">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-tasks default"></i></span>
                    Material Used
                </p>
            </header>
            <div class="card-content">
                <div class="card-data">
                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                        <tr>
                            <th title="Task date" width="15%">Task Name</th>
                            <th title="Task date" width="8%">Task date</th>                            
                            <th title="Task Material Name">Material Name</th>
                            {{-- <th title="Material Amount">Material Amount</th> --}}
                            {{-- <th title="Note">Note</th> --}}
                        </tr>
                        @foreach($material_used as $material)
                            <tr>
                                <td title="Task ID">
                                    <a href="{{ route('tasks.show', $material->task_id) }}" target="_blank">
                                        {{ \Tritiyo\Task\Models\Task::where('id', $material->task_id)->first()->task_name }}                                        
                                    </a>
                                </td>
                                <td title="Task date">
                                    {{ \Tritiyo\Task\Models\Task::where('id', $material->task_id)->first()->task_for }}
                                </td>
                                <td title="Task Material Name">
                                    @php
                                        $materialsss = explode(',', $material->materialsss)
                                    @endphp
                                    @foreach($materialsss as $m)
                                        {{ !empty($m) ? \Tritiyo\Material\Models\Material::where('id', $m)->first()->name : '' }}<br/>
                                    @endforeach
                                    
                                    
                                </td>
                                {{-- <td title="Material Rent">
                                    {{ $material->material_amount }}
                                </td> --}}
                                {{-- <td title="Note">
                                    {{ $material->material_note }}
                                </td> --}}
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    @endif

     <!-- Purchase -->
    <?php 
        $gettaskID = Tritiyo\Task\Models\TaskSite::where('site_id', $site->id)->groupBy('task_id')->get();
    ?>
    
        <div class="card tile is-child" style="margin-top: 15px !important;">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="fas fa-tasks default"></i></span>
                    Purchase
                </p>
            </header>
            <div class="card-content">
                <div class="card-data">
                        @foreach($gettaskID as $t)
                            @php
                                $taskId = $t->task_id;
                                $obr = new Tritiyo\Task\Helpers\RequisitionData('requisition_edited_by_accountant', $taskId);
                                $purchases = $obr->getPurchaseData();
                            @endphp    
                                @if(!empty($purchases))              
                                    <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                        <tr>
                                            <th title="Task name" width="15%">Task Name</th>
                                            <th title="Task date" width="8%">Task Name</th>
                                            <th title="PA Note">Purchase Note</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="{{ route('tasks.show', $taskId) }}" target="_blank">
                                                    {{ \Tritiyo\Task\Models\Task::where('id', $taskId)->first()->task_name }}                                        
                                                </a>
                                            </td>
                                            <td>{{ \Tritiyo\Task\Models\Task::where('id', $taskId)->first()->task_for }}</td>
                                            <td>
                                                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                                    @foreach($purchases as $purchase)
                                                        <tr>
                                                            <td>
                                                                {{$purchase->pa_note}}
                                                            </td>
                                                            
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>  
                                    </table>
                                @endif
                        @endforeach
                </div>
            </div>
        </div>
   
    

    <!-- Transport -->
    
    <div class="card tile is-child" style="margin-top: 15px !important;">
        <header class="card-header">
            <p class="card-header-title">
                <span class="icon"><i class="fas fa-tasks default"></i></span>
                Transport
            </p>
        </header>
        <div class="card-content">
            <div class="card-data">
                    @foreach($gettaskID as $t)
                        @php
                            $taskId = $t->task_id;
                            $obr = new Tritiyo\Task\Helpers\RequisitionData('requisition_edited_by_accountant', $taskId);
                            $transports = $obr->getTransportData();
                        @endphp    
                            @if(!empty($transports))              
                                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                    <tr>
                                        <th title="Task name" width="15%">Task Name</th>
                                        <th title="Task date" width="8%">Task Name</th>
                                        <th title="PA Note">Transport Information</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="{{ route('tasks.show', $taskId) }}" target="_blank">
                                                {{ \Tritiyo\Task\Models\Task::where('id', $taskId)->first()->task_name }}                                        
                                            </a>
                                        </td>
                                        <td>{{ \Tritiyo\Task\Models\Task::where('id', $taskId)->first()->task_for }}</td>
                                        <td>
                                            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                                                <tr>
                                                    <td title="Task name" width="25%">Where To Where</th>
                                                    <td title="Task date" width="15%">Transport Type</th>
                                                    <td title="PA Note">Transport Note</th>
                                                </tr>
                                                @foreach($transports as $transport)
                                                    <tr>
                                                        <td>
                                                            {{$transport->where_to_where}}
                                                        </td>
                                                        <td>
                                                            {{$transport->transport_type}}
                                                        </td>
                                                        <td>
                                                            {{$transport->ta_note}}
                                                        </td>
                                                        
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>  
                                </table>
                            @endif
                    @endforeach
            </div>
        </div>
    </div>

    

    
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
