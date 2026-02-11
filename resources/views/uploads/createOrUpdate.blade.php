@extends('layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header">
        {{ isset($item) ? 'Edit PDF' : 'Add PDF' }}
    </h5>
    <div class="card-body">
        <form action="{{ isset($item) ? route(request()->segment(1) . '.update', $item->id) : route(request()->segment(1) . '.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method(isset($item) ? 'PATCH' : 'POST')

            <div class="form-group">
                <label for="name">Circular Number/Guideline Number/Form Number</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $item->name ?? '') }}" required>
            </div>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required>{{ old('description', $item->description ?? '') }}</textarea>
            </div>
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror

            <div class="form-group">
                <label for="fileToUpload">Upload File</label>
                <input type="file" class="form-control" id="fileToUpload" name="fileToUpload" accept="application/pdf">
                @if(isset($item->file_url))
                    <br>
                    <embed src="{{ asset('pdfs/'.$item->file_url) }}" width="100" height="150" type="application/pdf">
                @endif
            </div>
            @error('fileToUpload') <small class="text-danger">{{ $message }}</small> @enderror

            <div class="form-group">
                <label for="released_year">Released Year</label>
                <select class="form-control" id="released_year" name="released_year">
                    @for ($year = 1950; $year <= date('Y'); $year++)
                        <option value="{{ $year }}" {{ old('released_year', $item->released_year ?? '') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
            @error('released_year') <small class="text-danger">{{ $message }}</small> @enderror

            <div class="form-group">
                <label for="releasedby">Released By</label>
                <select class="form-control" id="releasedby" name="releasedby" required>
                    <option value="">--Select--</option>
                    <option value="pubad" {{ old('releasedby', $item->releasedby ?? '') == 'pubad' ? 'selected' : '' }}>PUBAD</option>
                    <option value="moe" {{ old('releasedby', $item->releasedby ?? '') == 'moe' ? 'selected' : '' }}>MOE</option>
                    <option value="pmoe_ep" {{ old('releasedby', $item->releasedby ?? '') == 'pmoe_ep' ? 'selected' : '' }}>PMOE-EP</option>
                    <option value="pde_ep" {{ old('releasedby', $item->releasedby ?? '') == 'pde_ep' ? 'selected' : '' }}>PDE-EP</option>
                    <option value="zone" {{ old('releasedby', $item->releasedby ?? '') == 'zone' ? 'selected' : '' }}>Zone</option>
                    <option value="others" {{ old('releasedby', $item->releasedby ?? '') == 'others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>
            @error('releasedby') <small class="text-danger">{{ $message }}</small> @enderror

            <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">--Select--</option>
                    <option value="circular" {{ old('type', $item->type ?? '') == 'circular' ? 'selected' : '' }}>Circular</option>
                    <option value="guideline" {{ old('type', $item->type ?? '') == 'guideline' ? 'selected' : '' }}>Guideline</option>
                    <option value="circular_and_guideline" {{ old('type', $item->type ?? '') == 'circular_and_guideline' ? 'selected' : '' }}>Circular and Guideline</option>
                    <option value="form" {{ old('type', $item->type ?? '') == 'form' ? 'selected' : '' }}>Form or Application</option>
                </select>
            </div>
            @error('type') <small class="text-danger">{{ $message }}</small> @enderror

            <div class="form-group">
                <label for="is_website">Is Website</label>
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="is_website" 
                        name="is_website" 
                        value="1" 
                        {{ old('is_website', isset($item) ? $item->is_website : 1) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="is_website">Yes</label>
                </div>
            </div>

            <button type="submit" class="btn btn-success">{{ isset($item) ? 'Update' : 'Add' }}</button>
        </form>
    </div>
</div>
@endsection
