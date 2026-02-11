@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($buildingRepairCategory) ? 'Edit Repair Category' : 'Add Repair Category' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($buildingRepairCategory) ? route('building-repair-categories.update', $buildingRepairCategory->id) : route('building-repair-categories.store') }}" method="POST">
            @csrf
            @if(isset($buildingRepairCategory)) @method('PUT') @endif

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $buildingRepairCategory->name ?? '') }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($buildingRepairCategory) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
