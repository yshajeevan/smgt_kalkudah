@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
      <div class="col-md-12">
        @include('layouts.notification')
      </div>
    </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">{{ isset($designation) ? 'Edit a designation' : 'Create a new designation' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($designation) ? route('designation.update',$designation->id) : route('designation.store') }}" id="ds_form" name="ds_form" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf
            @method(isset($designation)? 'PUT':'POST')
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group text-dark">
                        <label for="" class="control-label">Designation</label>
                        <input type="text" class="form-control form-control-sm" name="designation" id="designation" value="{{ old('designation', isset($designation) ? $designation->designation : '') }}">
                        @error('designation')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group text-dark">
                        <label for="" class="control-label">Category</label>
                        <input type="text" class="form-control form-control-sm" placeholder="OAC, ONAC, SAC, ONACM, TC, SPC" name="catg" id="catg" value="{{ old('catg', isset($designation) ? $designation->catg : '') }}">
                        @error('catg')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group text-dark">
                        <label for="" class="control-label">Approved Cadre</label>
                        <input type="text" class="form-control form-control-sm" name="app_cadre" id="app_cadre" value="{{ old('app_cadre', isset($designation) ? $designation->app_cadre : '') }}">
                        @error('app_cadre')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group" align="center">
                      <input type="submit" id="saveBtn" class="btn btn-warning" value="{{ isset($dsdivision) ? 'Update': 'Add' }}">
                    </div>
                </div>
            </div>
        </form>
    </div>    
@endsection

@push('styles')
<style>

</style>
@endpush

@push('scripts')
<script type="text/javascript">
    var form = document.getElementById('ds_form');
    var submitButton = document.getElementById('submit');
    form.addEventListener('submit', function() {
       submitButton.setAttribute('disabled', 'disabled');
       submitButton.value = 'Please wait...';
    }, false);
</script>
@endpush