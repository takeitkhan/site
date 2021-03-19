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
            @foreach($sites as $site)
            {{-- @dump($site) --}}
                <div class="column is-2">
                    <div class="borderedCol">
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong>
                                            <strong>Code: </strong>
                                            <a href="{{ route('sites.show', $site->id) }}"
                                               title="View route">
                                                {{ $site->site_code }}
                                            </a>
                                        </strong>
                                        <br/>
                                        <small>
                                            <strong>Location: </strong> {{ $site->location }}
                                            <br/>
                                            <strong>Project: </strong>
                                            @php
                                                $project = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()
                                            @endphp
                                            {{  $project->name }}
                                            <br/>
                                            <strong>Task Created: </strong>
                                            {{ $site->created_at }}
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
                                        @if(auth()->user()->isAdmin(auth()->user()->id) || auth()->user()->isApprover(auth()->user()->id))
                                            <a href="{{ route('sites.edit', $site->id) }}"
                                               class="level-item"
                                               title="View all transaction">
                                                <span class="icon is-info is-small"><i class="fas fa-edit"></i></span>
                                            </a>
                                        @endif

                                        {{--                                        {!! delete_data('sites.destroy',  $site->id) !!}--}}
                                    </div>
                                </nav>
                            </div>
                        </article>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pagination_wrap pagination is-centered">
            {{$sites->links('pagination::bootstrap-4')}}
        </div>
    @endif
@endsection
