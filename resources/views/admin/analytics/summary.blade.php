@extends('layouts.master')

@section('main-content')
<div class="container-flex">
    @include('admin.analytics.partials.analytics_header', ['activeUsers' => true])

    <div class="row">
        <div class="col-sm-12">
            @include('admin.analytics.partials.visitors_views')
        </div>
    </div>

    {{-- locations + devices_category --}}
    <div class="row">
        <div class="col-md-7">
            @include('admin.analytics.partials.locations')
        </div>
        <div class="col-md-5">
            @include('admin.analytics.partials.devices_category')
        </div>
    </div>
    <!-- Devices -->
    <div class="row">
        <div class="col-md-6">
            @include('admin.analytics.partials.browsers')
        </div>
        <div class="col-sm-6">
            @include('admin.analytics.partials.devices')
        </div>
    </div>
    <!-- Visit-refferals -->
    <div class="row">
        <div class="col-md-6">
            @include('admin.analytics.partials.visited_pages')
        </div>

        <div class="col-md-6">
            @include('admin.analytics.partials.referrers')
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            @include('admin.analytics.partials.keywords')
        </div>
    </div>
    <!-- demographycs -->
    <div class="row">
        <div class="col-md-6">
            @include('admin.analytics.partials.gender')
        </div>

        <div class="col-md-6">
            @include('admin.analytics.partials.age')
        </div>
    </div>
    <!-- interests -->
    <div class="row">
        <div class="col-md-6">
            @include('admin.analytics.partials.interests_affinity')
        </div>

        <div class="col-md-6">
            @include('admin.analytics.partials.interests_market')
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            @include('admin.analytics.partials.interests_other')
        </div>
    </div>

    @if(config('app.env') !== 'local')
        @include('partials.analytics')
    @endif
</div>
@endsection

@push('styles')
<link href="{{asset('css/admin.css')}}" rel="stylesheet"">
<style>
/*widget css*/
.container-flex{
    padding: 10px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript" charset="utf-8" src="{{asset('js/admin.js')}}"></script>

@endpush


