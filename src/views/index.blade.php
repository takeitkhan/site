@extends('layouts.app')

@section('title')
    Sites
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
            'spTitle' => 'Sites',
            'spSubTitle' => 'all sites here',
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
            'spPlaceholder' => 'Search sites...',
            'spAddUrl' => route('sites.create'),
            'spAllData' => route('sites.index'),
            'spSearchData' => route('sites.search'),
            'spMessage' => $message = $message ?? NULl,
            'spStatus' => $status = $status ?? NULL
        ])
    </nav>
</section>

@section('column_left')
    @if(!empty($sites))
        <div class="columns is-multiline">
            @php
                if(auth()->user()->isManager(auth()->user()->id)) {
                    $manager_id = auth()->user()->id;
                    if(request()->get('key')) {
                        $default = [
                            'search_key' => request()->get('key') ?? '',
                            'limit' => 10,
                            'offset' => 0
                        ];
                        $no = $default;
                        /*
                        $sitesss = \Tritiyo\Site\Models\Site::leftjoin('projects', 'projects.id', 'sites.project_id')
                            ->select('sites.*', 'projects.name', 'projects.code', 'projects.type', 'projects.customer', '(SELECT name FROM users WHERE id = projects.manager) AS manager')
                            ->where('project_id', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('sites.location', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('sites.site_code', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('sites.material', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('sites.site_head', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('sites.budget', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('sites.completion_status', 'LIKE', '%' . $no['search_key'] . '%')

                            ->orWhere('projects.name', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('projects.code', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('projects.type', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('projects.customer', 'LIKE', '%' . $no['search_key'] . '%')
                            ->orWhere('manager', 'LIKE', '%' . $no['search_key'] . '%')
                            //->toSql();
                            ->paginate('48');
                        */
                        //Nipun
                        $key = $no['search_key'];
                        $sitesss = \Tritiyo\Site\Models\Site::leftjoin('projects', 'projects.id', 'sites.project_id')
                                    ->leftjoin('users', 'users.id', 'projects.manager')
                                    ->select('sites.*', 'projects.name', 'projects.code', 'projects.type', 'projects.customer','users.name')
                                    ->where('sites.project_id' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('sites.location' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('sites.site_code' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('sites.material' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('sites.site_head' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('sites.budget' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('sites.completion_status' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('projects.name' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('projects.code' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('projects.type' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('projects.customer' ,'LIKE', '%'.$key.'%')
                                    ->orWhere('users.name' ,'LIKE', '%'.$key.'%')
                                    ->paginate('48');
                    } else {
                        $sitesss = \DB::table('sites')->leftJoin('projects', 'projects.id', 'sites.project_id')
                                    ->select('sites.*', 'projects.manager')
                                    ->where('projects.manager', $manager_id)
                                    ->groupBy('sites.project_id')
                                    ->groupBy('sites.id')
                                    ->paginate(30);
                    }
                } else {
                    $sitesss = $sites;
                }
            @endphp
            @foreach($sitesss as $site)
                @include('site::index_template')
            @endforeach
        </div>
        <div class="pagination_wrap pagination is-centered">
            @if(Request::get('key'))
                {{ $sitesss->appends(['key' => Request::get('key')])->links('pagination::bootstrap-4') }}
            @else
                {{ $sitesss->links('pagination::bootstrap-4') }}
            @endif
        </div>
    @endif
@endsection
