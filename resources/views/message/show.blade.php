@extends('layouts.master')

@section('main-content')
<div class="card">
  <h5 class="card-header">Message</h5>
  <div class="card-body">
    @if($message)
        @if($message->sender->photo)
        <img src="{{$message->sender->photo}}" class="img-profile rounded-circle" style="margin-left:44%; width:100px;"">
        @else 
        <img src="{{asset('backend/img/avatar.png')}}" class="rounded-circle " style="margin-left:44%;">
        @endif
        <div class="py-4">From <br>
           Name :{{$message->sender->name}}<br>
           Designation :{{$message->sender->desig}}<br>
           Email :{{$message->sender->email}}<br><br>
           Date :{{$message->sender->created_at}}
        <hr/>
  <h5 class="text-center" style="text-decoration:underline"><strong>Subject :</strong> {{$message->subject}}</h5>
        <p class="py-5">{{$message->message}}</p>

        @if($message->file)
        <a href="{{ route('message.attachment', ['filename' => $message->file]) }}" target="_blank">
            <button class="btn"><i class="fa fa-download"></i>{{$message->file}}</button>
        </a>
        @endif
    @endif

  </div>
</div>
@endsection