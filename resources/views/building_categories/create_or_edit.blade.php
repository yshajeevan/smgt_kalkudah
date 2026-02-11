@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($buildingCategory) ? 'Edit Building Category' : 'Add Building Category' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($buildingCategory) ? route('building-categories.update', $buildingCategory->id) : route('building-categories.store') }}" method="POST">
            @csrf
            @if(isset($buildingCategory)) @method('PUT') @endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $buildingCategory->name ?? '') }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($buildingCategory) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
