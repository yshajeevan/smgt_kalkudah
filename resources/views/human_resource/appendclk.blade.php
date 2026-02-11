@extends('layouts.master')

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h6 class="m-0 font-weight-bold text-primary float-left">Append institute_id(Employee) for PF and ACT Clerks</h6>
        </div> 
    </div>  
      <form action="{{route('employee.updateclerk')}}" id="append_form" name="append_form" method="post">
        @csrf
        <div class="form-group">
            <input type="submit" id="saveBtn" class="btn btn-warning" value="Append">
        </div>
    </form>
@endsection
