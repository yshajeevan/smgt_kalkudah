@extends('layouts.master')

@section('main-content')
<div class="card-body box-profile">
  <ul>
    <li class="list-group-item scroll">
    <h4 style="color:green">Password reset successfully! Click below buttion to return home.</h4>
    </li>

    <li class="list-group-item">
        <a href="{{ route('/') }}" class="btn btn-primary">Home</a>
    </li>
  </ul>
</div>
@endsection