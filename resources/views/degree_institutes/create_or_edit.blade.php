@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($degInstitute) ? 'Edit Institute' : 'Add Institute' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($degInstitute) ? route('deg-institutes.update', $degInstitute->id) : route('deg-institutes.store') }}" method="POST">
            @csrf
            @if(isset($degInstitute)) @method('PUT') @endif

            <div class="form-group">
                <label for="eduinsti">Institute</label>
                <input type="text" name="eduinsti" class="form-control" value="{{ old('eduinsti', $degInstitute->eduinsti ?? '') }}">
                @error('eduinsti') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($degInstitute) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
