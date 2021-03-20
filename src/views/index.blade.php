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
                    $sitesss = \DB::table('sites')->leftJoin('projects', 'projects.id', 'sites.project_id')
                                    ->select('sites.*', 'projects.manager')
                                    ->where('projects.manager', $manager_id)
                                    ->groupBy('sites.project_id')
                                    ->groupBy('sites.id')
                                    ->paginate(30);
                } else {
                    $sitesss = $sites;
                }
            @endphp
            @foreach($sitesss as $site)
                @include('site::index_template')
            @endforeach
        </div>
        <div class="pagination_wrap pagination is-centered">
            {{ $sitesss->links('pagination::bootstrap-4') }}
        </div>
    @endif
@endsection
