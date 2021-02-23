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
                <span class="icon"><i class="mdi mdi-account default"></i></span>
                Main Site Data
            </p>
        </header>
        <div class="card-content">
            <div class="card-data">
                <div class="columns">
                    <div class="column is-2">Project Name</div>
                    <div class="column is-1">:</div>
                    <div class="column">
                        {{ \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()->name }}
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-2">Loccation</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $site->location }}</div>
                </div>
                <div class="columns">
                    <div class="column is-2">Site Code</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $site->site_code }}</div>
                </div>
                <div class="columns">
                    <div class="column is-2">Budget</div>
                    <div class="column is-1">:</div>
                    <div class="column">{{ $site->budget }}</div>
                </div>
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
