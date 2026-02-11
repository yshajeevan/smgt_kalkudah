@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($roomType) ? 'Edit Room Type' : 'Add Room Type' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($roomType) ? route('room-types.update', $roomType->id) : route('room-types.store') }}" method="POST">
            @csrf
            @if(isset($roomType)) @method('PUT') @endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $roomType->name ?? '') }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($roomType) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
