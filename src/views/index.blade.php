@extends('layouts.app')

@section('title')
    Sites
@endsection

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
    <div class="columns is-multiline">
        @if(!empty($sites))
            @foreach($sites as $site)
                <div class="column is-4">
                    <div class="borderedCol">
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong>
                                            <a href="{{ route('sites.show', $site->id) }}"
                                               title="View route">
                                               <strong>Location: </strong>  {{ $site->location }},
                                            </a>
                                        </strong>
                                        <br/>
                                        <small>
                                            <strong>Code: </strong> {{ $site->site_code }},
                                            <strong>Project: </strong> 
                                            @php $project = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first() @endphp
                                            {{  $project->name }}
                                        </small>
                                        <br/>
                                        <small>
                                            <strong>Budget:</strong> {{ $site->budget }}
                                        </small>
                                        <br/>
                                    </p>
                                </div>
                                <nav class="level is-mobile">
                                    <div class="level-left">
                                        <a href="{{ route('sites.show', $site->id) }}"
                                           class="level-item"
                                           title="View user data">
                                            <span class="icon is-small"><i class="fas fa-eye"></i></span>
                                        </a>
                                        <a href="{{ route('sites.edit', $site->id) }}"
                                           class="level-item"
                                           title="View all transaction">
                                            <span class="icon is-info is-small"><i class="fas fa-edit"></i></span>
                                        </a>                                        

                                        {!! delete_data('sites.destroy',  $site->id) !!}
                                    </div>
                                </nav>
                            </div>
                        </article>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
