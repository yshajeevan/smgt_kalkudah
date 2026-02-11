@extends('layouts.master')
@section('main-content')
<div class="card">
  <h5 class="card-header">Message</h5>
  <div class="card-body">
    @if($notification)
        <div class="py-4">From: <br>
           Description :{{$notification->description}}<br>
           Causer :{{$notification->causer_id}}<br>
        </div>
        <hr/>
  <h5 class="text-center" style="text-decoration:underline"><strong>Subject :</strong>Date of Modify</h5>
        <p class="py-5">{{$notification->updated_at}}</p>

    @endif

  </div>
</div>
@endsection