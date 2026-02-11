@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($degree) ? 'Edit Degree' : 'Add Degree' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($degree) ? route('degrees.update', $degree->id) : route('degrees.store') }}" method="POST">
            @csrf
            @if(isset($degree)) @method('PUT') @endif

            <div class="form-group">
                <label for="degree">Degree</label>
                <input type="text" name="degree" class="form-control" value="{{ old('degree', $degree->degree ?? '') }}">
                @error('degree') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($degree) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
