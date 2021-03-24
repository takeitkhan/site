<div class="card tile is-child">
    <div class="card-content">
        <div class="card-data">
            <div class="level">
                <div class="level-left">
                    <strong>Site based tasks</strong>
                </div>
                <div class="level-right">
                    <div class="level-item ">
                        <form method="get" action="{{ route('sites.show', $site->id) }}">
                            @csrf

                            <div class="field has-addons">
                                <a href="{{ route('download_excel_site') }}?id={{ $site->id }}&daterange={{ request()->get('daterange') ??  date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days')) . ' - ' . date('Y-m-d') }}"
                                   class="button is-primary is-small">
                                    Download as excel
                                </a>
                                <div class="control">
                                    <input class="input is-small" type="text" name="daterange" id="textboxID"
                                           value="{{ request()->get('daterange') ?? null }}">
                                </div>
                                <div class="control">
                                    <input name="search" type="submit"
                                           class="button is-small is-primary has-background-primary-dark"
                                           value="Search"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <tr>
                <th>Task Name</th>
                <th>Task For</th>
                <th>Task Type</th>
                <th>Vehicle Used</th>
                <th>Material Used</th>
                <th>Purchase Note</th>
{{--                <th>Purchase Amount</th>--}}
                <th>Budget</th>
                <th>Expense</th>
                <th>Completion Status</th>
            </tr>
            <?php //echo request()->get('daterange');?>
            @php

                if (request()->get('daterange')) {
                        $dates = explode(' - ', request()->get('daterange'));
                        $start = $dates[0];
                        $end = $dates[1];

                    //$tasks = \Tritiyo\Task\Models\Task::where('site_id', $site->id)->whereBetween('task_for', [$start, $end])->groupBy('task_id')->paginate(50);
                     $tasks = \Tritiyo\Task\Models\TaskSite::leftjoin('tasks', 'tasks.id', 'tasks_site.task_id')
                                                            ->select('tasks_site.*', 'tasks.task_for')
                                                            ->where('tasks_site.site_id', $site->id)
                                                            ->whereBetween('tasks.task_for', [$start, $end])
                                                            ->groupBy('tasks_site.task_id')
                                                            ->get();


                } else {
                    $start = date('Y-m-d', strtotime(date('Y-m-d'). ' - 30 days'));
                    $end = date('Y-m-d');
                    //$tasks = \Tritiyo\Task\Models\Task::where('project_id', $project->id)->whereBetween('task_for', [$start, $end])->paginate(50);
                    $tasks = \Tritiyo\Task\Models\TaskSite::leftjoin('tasks', 'tasks.id', 'tasks_site.task_id')
                                                            ->select('tasks_site.*', 'tasks.task_for')
                                                            ->where('tasks_site.site_id', $site->id)
                                                            ->whereBetween('tasks.task_for', [$start, $end])
                                                            ->groupBy('tasks_site.task_id')
                                                            ->paginate('50');
                }

              //dd($tasks);
            @endphp

            @foreach($tasks as $data)
                @php
                    $task_name = \Tritiyo\Task\Models\Task::where('id', $data->task_id)->first()->task_name;
                    $task_for = \Tritiyo\Task\Models\Task::where('id', $data->task_id)->first()->task_for;
                    $task_type = \Tritiyo\Task\Models\Task::where('id', $data->task_id)->first()->task_type;
                @endphp
                <tr>
                    <td>  <a href="{{route('tasks.show', $data->task_id)}}">{{$task_name}}</a></td>
                    <td>{{$task_for}}</td>
                    <td>{{$task_type}}</td>
                    <td>
                        @php
                        $vehicleUsed = \Tritiyo\Task\Models\TaskVehicle::leftjoin('vehicles', 'vehicles.id', 'tasks_vehicle.vehicle_id')
                                                    ->select('vehicles.name')
                                                    ->where('tasks_vehicle.task_id', $data->task_id)
                                                    ->get()->toArray();
                        echo implode('<br>',array_column($vehicleUsed, 'name'));
                        @endphp
                    </td>
                    <td>

                        @php
                            $materialUsed = \Tritiyo\Task\Models\TaskMaterial::leftjoin('materials', 'materials.id', 'tasks_material.material_id')
                                                        ->select('materials.name')
                                                        ->where('tasks_material.task_id', $data->task_id)
                                                        ->get()->toArray();
                            echo implode('<br>',array_column($materialUsed, 'name'));
                        @endphp
                    </td>
                    @php
                        $taskId = $data->task_id;
                        $obr = new Tritiyo\Task\Helpers\RequisitionData('requisition_edited_by_accountant', $taskId);
                        $purchases = $obr->getPurchaseData();
                    @endphp
                    <td>
                        @if(!empty($purchases))
                            <?php echo implode('<br>',array_column($purchases, 'pa_note'));?>
                        @endif
                    </td>
{{--                    <td>--}}
{{--                        <?php echo implode('<br>',array_column($purchases, 'pa_amount'));?>--}}
{{--                    </td>--}}
                    <td>
                        @php
                            $total_project_budget = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()->budget;
                            $total_sites = \Tritiyo\Site\Models\Site::where('project_id', '=', $site->project_id)->get()->count();
                            echo $total_project_budget/$total_sites;
                        @endphp
                    </td>
                    <td>
                        {{  (new Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $taskId))->getTotal()}}
                    </td>
                    <td>
                        {{$site->completion_status}}
                    </td>
                </tr>

            @endforeach

        </table>
        <div class="pagination_wrap pagination is-centered">
            {{ $tasks->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@section('cusjs')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <script type="text/javascript">
        document.getElementById('textboxID').select();
    </script>

    <script>
        $(function () {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD'
                }
            }, function (start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>
@endsection
