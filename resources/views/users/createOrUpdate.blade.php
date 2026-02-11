@extends('layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header">{{ isset($item) ? 'Edit User' : 'Add User' }}</h5>
    <div class="card-body">
    <form action="{{ isset($item) ? route('user.update',$item->id) : route('user.store') }}" method="POST" enctype="multipart/form-data">
      {{csrf_field()}}
      @if(empty($item))
      <div class="search-wrapper">
        <i class="fa fa-search" aria-hidden="true"></i>
	      <input type="text" id="employee_search" class="search-box" placeholder="Enter NIC number..." />
		    <button class="close-icon" type="reset"></button>
      </div>
      @endif
        <div class="form-group">
            <label for="employee_id" class="col-form-label">Employee ID</label>
            <input id="employee_id" type="text" name="employee_id" value="{{old('employee_id', isset($item) ? $item->employee_id : '')}}" class="form-control" required>
            @error('employee_id')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="name" class="col-form-label">Name</label>
            <input id="name" type="text" name="name" value="{{old('name', isset($item) ? $item->employee->surname : '')}}" class="form-control" required>
            @error('name')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="designation" class="col-form-label">Designation</label>
            <input id="designation" type="text" name="designation" value="{{old('designation', isset($item) ? $item->employee->designation->designation : '')}}" class="form-control" required>
            @error('designation')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="institute" class="col-form-label">Institute</label>
            <input id="institute" type="text" name="institute" value="{{old('institute', isset($item) ? $item->employee->institute->institute : '')}}" class="form-control" required>
            <input id="institute_id" type="text" name="institute_id" value="{{old('institute_id', isset($item) ? $item->employee->institute->id : '')}}" class="form-control" required>
            @error('institute')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="mobile" class="col-form-label">Mobile</label>
            <input id="mobile" type="text" name="mobile" placeholder="Enter Mobile Number"  value="{{old('mobile', isset($item) ? $item->employee->mobile : '')}}" class="form-control">
            @error('mobile')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        @php
          $employeePhoto = public_path('images/employees/' . ($item->employee_id ?? '') . '.jpg');
          if (isset($item) && file_exists($employeePhoto)) {
              $photoUrl = asset('images/employees/' . $item->employee_id . '.jpg');
          } elseif (file_exists(public_path('images/avatar.jpg'))) {
              $photoUrl = asset('images/avatar.jpg');
          } else {
              $photoUrl = asset('backend/img/avatar.png');
          }
      @endphp

      <div class="form-group">
          <div class="create-user-photo" id="photo" style="background-image: url('{{ $photoUrl }}');"></div>
          @error('photo')
            <span class="text-danger">{{ $message }}</span>
          @enderror
      </div>
    </div>
    
<div class="card">
  <div class="card-body">
        <div class="form-group">
            <label for="email" class="col-form-label">Email</label>
            <input id="email" type="email" name="email" placeholder="Enter email"  value="{{old('email', isset($item) ? $item->email : '')}}" class="form-control" required>
            @error('email')
                <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="password" class="col-form-label">Password</label>
          <input id="password" type="password" name="password" placeholder="Enter password" class="form-control">
          @error('password')
            <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
            <label for="confirm-password" class="col-form-label">Confirm Password</label>
            <input id="confirm-password" type="password" name="confirm-password" placeholder="Enter confirm password" class="form-control">
            @error('password')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
          <label for="roles" class="col-form-label">Role</label>
          <select name="roles[]" id="roles" class="select-2 form-control" style="width:100%;" multiple>
            <option>--Select User Role--</option>
            @foreach($roles as $role)
              <option {{isset($userrole) ? in_array($role->name, $userrole) ? 'selected':'' : '$role->name'}}>{{$role->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
            <label for="is_active" class="col-form-label">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{isset($item) ? ($item->is_active=='1') ? 'selected' : '' : ''}}>Active</option>
                <option value="0" {{isset($item) ? ($item->is_active=='0') ? 'selected' : '' : ''}}>Inactive</option>
            </select>
            @error('is_active')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <br>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">{{ isset($item) ? 'Update': 'Add' }}</button>
        </div>
      </form>
    </div>
</div>
@endsection


@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
<style>
.create-user-photo
{
    /* position: relative; */
    border-radius: 50%;
    height: 100px;
    width: 100px;
    background-color: white;
    margin: auto;
    margin-top: 10px;
    background-size: cover;
    background-position: 50% 50%;
    -webkit-box-shadow: inset 0px 0px 5px 1px rgba(0,0,0,0.3);
    -moz-box-shadow: inset 0px 0px 5px 1px rgba(0,0,0,0.3);
    box-shadow: inset 0px 0px 5px 1px rgba(0,0,0,0.3);
    -webkit-transition: top 0.7s ease-in-out, background 0.15s ease;
    -moz-transition:    top 0.7s ease-in-out, background 0.15s ease;
    -o-transition:      top 0.7s ease-in-out, background 0.15s ease;
    -ms-transition:     top 0.7s ease-in-out, background 0.15s ease;
}

.search-box,.close-icon {
	position: relative;
	padding: 10px;
}
.search-wrapper {
	margin-top: 10px;
}
.search-wrapper i {
    position: absolute;
    z-index:2;
    margin-top: 12px;
    margin-left: 12px;
    pointer-events: none;
    font-size: 25px;
}
.search-box {
	width: 80%;
	border: 1px solid #ccc;
  outline: 0;
  border-radius: 15px;
  padding-left: 50px;
}
.search-box:focus {
	box-shadow: 0 0 15px 5px #b0e0ee;
	border: 2px solid #bebede;
}
.close-icon {
	border:1px solid transparent;
	background-color: transparent;
	display: inline-block;
	vertical-align: middle;
  outline: 0;
  cursor: pointer;
}
.close-icon:after {
	content: "X";
	display: block;
	width: 15px;
	height: 15px;
	position: absolute;
	background-color: #FA9595;
	z-index:1;
	right: 35px;
	top: 0;
	bottom: 0;
	margin: auto;
	padding: 2px;
	border-radius: 50%;
	text-align: center;
	color: white;
	font-weight: normal;
	font-size: 12px;
	box-shadow: 0 0 2px #E50F0F;
	cursor: pointer;
}
.search-box:not(:valid) ~ .close-icon {
	display: none;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" ></script>
<script>
$( "#employee_search" ).autocomplete({
    source: function( request, response ) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      // Fetch data
      $.ajax({
        url: "{{'/autocomplete-user-search'}}",
        type: 'post',
        dataType: "json",
        data: {
          search: request.term
        },
        success: function( data ) {
          response( data );
        }
      });
    },
    select: function (event, ui) {
      console.log(ui.item.photo);
            $('#employee_search').val(ui.item.label);  
            $('#employee_id').val(ui.item.value);
            $('#name').val(ui.item.fullname);
            $('#institute').val(ui.item.institute);
            $('#institute_id').val(ui.item.institute_id);
            $('#designation').val(ui.item.designation);
            $('#mobile').val(ui.item.mobile);
            $('#email').val(ui.item.email);
            $('.create-user-photo').css('background-image', 'url(images/employees/' + ui.item.value + '.jpg)'); 
            return false;
    }
  });

  $(document).ready(function() {
    $('.select-2').select2();
  });
</script>
@endpush