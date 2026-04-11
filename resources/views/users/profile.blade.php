@extends('layouts.master')

@section('title','Admin Profile')

@section('main-content')

<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
           @include('layouts.notification')
        </div>
    </div>
   <div class="card-header py-3">
     <h4 class=" font-weight-bold">Profile</h4>
     <ul class="breadcrumbs">
         <li><a href="{{route('/')}}" style="color:#999">Dashboard</a></li>
         <li><a href="" class="active text-primary">Profile Page</a></li>
     </ul>
   </div>
   <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="image">
                        @if($profile->employee->photo)
                        <img class="card-img-top img-fluid roundend-circle mt-4" style="border-radius:50%;height:80px;width:80px;margin:auto;" src="{{ asset('vfiles/profileimg/' . $profile->employee->photo) }}"alt="profile picture">
                        @else 
                        <img class="card-img-top img-fluid roundend-circle mt-4" style="border-radius:50%;height:80px;width:80px;margin:auto;" src="{{asset('backend/img/avatar.png')}}" alt="profile picture">
                        @endif
                    </div>
                    <div class="card-body mt-4 ml-2">
                      <h5 class="card-title text-left"><small><i class="fas fa-user"></i> {{$profile->name}}</small></h5>
                      <p class="card-text text-left"><small><i class="fas fa-envelope"></i> {{$profile->email}}</small></p>
                      <p class="card-text text-left"><small class="text-muted"><i class="fas fa-phone"></i> {{$profile->employee->mobile}}</small></p>
                    </div>
                  </div>
            </div>
            <div class="col-md-8">
                <form method="POST" enctype="multipart/form-data" action="{{route('profile-update',$profile->id)}}">
                    @csrf
                    <div class="form-group">
                        <label for="inputTitle" class="col-form-label">Display Name</label>
                      <input id="inputTitle" type="text" name="name" placeholder="Enter name"  value="{{$profile->name}}" class="form-control">
                      @error('name')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                      </div>
              
                      <div class="form-group">
                          <label for="inputEmail" class="col-form-label">Email (username)</label>
                        <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{$profile->email}}" class="form-control">
                        @error('email')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                      </div>

                      <div class="form-group">
                        <label class="col-form-label">Mobile</label>
                        <input type="text" name="mobile" value="{{ $profile->employee->mobile ?? '' }}" class="form-control">
                    </div>
              
                      <div class="form-group">
                        <label class="col-form-label">Photo</label>

                        <div class="mb-2">
                        <img id="preview"
                            src="{{ asset('vfiles/profileimg/' . ($profile->employee->photo ?? 'avatar.png')) }}"
                            style="height:80px;width:80px;border-radius:50%;object-fit:cover;">
                        </div>

                        <input type="file" name="photo" class="form-control" onchange="previewImage(event)">
                        @error('photo')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                      </div>
                    <button type="submit" class="btn btn-success btn-sm">Update</button>
                </form>
            </div>
        </div>
   </div>
</div>

@endsection

<style>
    .breadcrumbs{
        list-style: none;
    }
    .breadcrumbs li{
        float:left;
        margin-right:10px;
    }
    .breadcrumbs li a:hover{
        text-decoration: none;
    }
    .breadcrumbs li .active{
        color:red;
    }
    .breadcrumbs li+li:before{
      content:"/\00a0";
    }
    .image{
        background:url('{{asset('backend/img/background.jpg')}}');
        height:150px;
        background-position:center;
        background-attachment:cover;
        position: relative;
    }
    .image img{
        position: absolute;
        top:55%;
        left:35%;
        margin-top:30%;
    }
    i{
        font-size: 14px;
        padding-right:8px;
    }
  </style> 

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#lfm').filemanager('image');
</script>
@endpush