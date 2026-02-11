@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
      <div class="col-md-12">
        @include('layouts.notification')
      </div>
    </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">{{ isset($dsdivision) ? 'Edit a Ds Division' : 'Create a new Ds Division' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($dsdivision) ? route('dsdivision.update',$dsdivision->id) : route('dsdivision.store') }}" id="ds_form" name="ds_form" method="post">
            @csrf 
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group text-dark">
                        <label for="" class="control-label">DS Division</label>
                        <input type="text" class="form-control form-control-sm" name="ds" id="ds" value="{{ old('ds', isset($dsdivision) ? $dsdivision->ds : '') }}">
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