@extends('layouts.app')
@section('title')
    Edit Site
@endsection

<section class="hero is-white borderBtmLight">
    <nav class="level">
        @include('component.title_set', [
            'spTitle' => 'Edit Site',
            'spSubTitle' => 'Edit a single site',
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
    <article class="panel is-primary">
        <p class="panel-tabs">
            <a class="is-active">Site Information</a>
        </p>


        <div class="customContainer">
            {{ Form::open(array('url' => route('sites.update', $site->id), 'method' => 'PUT', 'value' => 'PATCH', 'id' => 'add_route', 'files' => true, 'autocomplete' => 'off')) }}
            <div class="columns">
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('project_id', 'Project', array('class' => 'label')) }}
                        <div class="control">
                            <?php $projects = \Tritiyo\Project\Models\Project::pluck('name', 'id')->prepend('Select Project', ''); ?>
                            {{ Form::select('project_id', $projects, $site->project_id ?? NULL, ['class' => 'input']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('location', 'Location', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('location', $site->location ?? NULL, ['class' => 'input', 'placeholder' => 'Enter location...']) }}
                        </div>
                    </div>
                </div>
                <div class="column is-3">
                    <div class="field">
                        {{ Form::label('site_code', 'Site Code', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('site_code', $site->site_code ?? NULL, ['class' => 'input', 'placeholder' => 'Enter Site Code...']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                 <div class="column is-3">
                    <div class="field">
                        {{ Form::label('budget', 'Budget', array('class' => 'label')) }}
                        <div class="control">
                            {{ Form::text('budget', $site->budget ?? NULL, ['class' => 'input', 'placeholder' => 'Enter budget...']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <div class="field is-grouped">
                        <div class="control">
                            <button class="button is-success is-small">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </article>
@endsection

@section('column_right')
    <article class="is-primary">
        <div class="box">
            <h1 class="title is-5">Important Note</h1>
            <p>
                Please select project manager and budget properly
            </p>
        </div>
    </article>
@endsection
