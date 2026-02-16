@extends('layouts.master')

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ isset($prof_qualification) ? 'Edit Qualification' : 'Add Qualification' }}
        </h6>
    </div>

    <div class="card-body">

        <form action="{{ isset($prof_qualification)
            ? route('prof-qualifications.update', $prof_qualification->id)
            : route('prof-qualifications.store') }}"
            method="POST">

            @csrf
            @if(isset($prof_qualification))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="name">Qualification Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $prof_qualification->name ?? '') }}"
                       required>

                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                {{ isset($prof_qualification) ? 'Update' : 'Save' }}
            </button>

            <a href="{{ route('prof-qualifications.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

@endsection
