@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($staff) ? 'Edit Staff' : 'Add Staff' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($staff) ? route('staff.update', $staff->id) : route('staff.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($staff)) @method('PUT') @endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $staff->name ?? '') }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="designation_id">Designation</label>
                <input type="text" name="designation" class="form-control" value="{{ old('designation', $staff->designation ?? '') }}">
                @error('designation')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="branch">Branch</label>
                <input type="text" name="branch" class="form-control" value="{{ old('branch', $staff->branch ?? '') }}">
                @error('branch')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email ?? '') }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone ?? '') }}">
                @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="whatsapp">WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $staff->whatsapp ?? '') }}">
                @error('whatsapp')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" name="image" class="form-control">
                @if(isset($staff) && $staff->image)
                    <img src="{{ asset('images/staff_officers/' . $staff->image) }}" alt="" class="img-thumbnail mt-2" width="150">
                @endif
                @error('image')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_website">Display on Website?</label>
                <select name="is_website" class="form-control">
                    <option value="0" {{ isset($staff) && $staff->is_website == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ isset($staff) && $staff->is_website == 1 ? 'selected' : '' }}>Yes</option>
                </select>
                @error('is_website')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="list_order">List Order</label>
                <input type="number" name="list_order" class="form-control" value="{{ old('list_order', $staff->list_order ?? '') }}">
                @error('list_order')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($staff) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
