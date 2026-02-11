@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($buildingType) ? 'Edit Building Type' : 'Add Building Type' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($buildingType) ? route('building-types.update', $buildingType->id) : route('building-types.store') }}" method="POST">
            @csrf
            @if(isset($buildingType)) @method('PUT') @endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $buildingType->name ?? '') }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($buildingType) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
