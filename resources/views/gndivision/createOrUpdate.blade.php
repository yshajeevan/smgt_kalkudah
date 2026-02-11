@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
      <div class="col-md-12">
        @include('layouts.notification')
      </div>
    </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">{{ isset($gndivision) ? 'Edit a Ds Division' : 'Create a new GN Division' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($gndivision) ? route('gndivision.update',$gndivision->id) : route('gndivision.store') }}" id="ds_form" name="ds_form" method="post">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label><strong>DS Division:</strong></label>
                        <select name="dsdivision_id" id="dsdivision_id" class="form-control form-control-sm">
                            <option value="">--Select DS Division--</option>
                            @foreach ($dsdivisions as $dsdivision)
                            <option value="{{$dsdivision->id}}" {{(isset($gndivision) && $gndivision->dsdivision_id == $dsdivision->id)  ? 'selected' : ''}}>{{$dsdivision->ds}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group text-dark">
                        <label for="" class="control-label">GN Division</label>
                        <input type="text" class="form-control form-control-sm" name="gn" id="gn" value="{{ old('gn', isset($gndivision) ? $gndivision->gn : '') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group text-dark">
                        <label for="" class="control-label">GPS Location (eg:7.738091,81.5761202)</label>
                        <input type="text" class="form-control form-control-sm" name="gpslocation" id="gpslocation" value="{{ old('gpslocation', isset($gndivision) ? $gndivision->gpslocation : '') }}">
                    </div>
                </div>
            </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group" align="center">
                      <input type="submit" id="saveBtn" class="btn btn-warning" value="{{ isset($gndivision) ? 'Update': 'Add' }}">
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