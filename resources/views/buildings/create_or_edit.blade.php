@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($building) ? 'Edit Building' : 'Add Building' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($building) ? route('buildings.update', $building->id) : route('buildings.store') }}" method="POST">
            @csrf
            @if(isset($building)) @method('PUT') @endif

            <div class="form-group">
                <label for="institute_id">Institute</label>
                <select name="institute_id" class="form-control">
                    <option value="">Select an Institute</option>
                    @foreach($institutes as $institute)
                        <option value="{{ $institute->id }}" {{ (isset($building) && $building->institute_id == $institute->id) ? 'selected' : '' }}>
                            {{ $institute->institute }}
                        </option>
                    @endforeach
                </select>
                @error('institute_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $building->name ?? '') }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="size">Size</label>
                <input type="text" name="size" class="form-control" value="{{ old('size', $building->size ?? '') }}">
                @error('size') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="building_category_id">Building Category</label>
                <select name="building_category_id" class="form-control">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (isset($building) && $building->building_category_id == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('building_category_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="building_type_id">Building Type</label>
                <select name="building_type_id" class="form-control">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ (isset($building) && $building->building_type_id == $type->id) ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('building_type_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="usage">Usage</label>
                <select name="usage" class="form-control">
                    <option value="1" {{ (isset($building) && $building->usage == 1) ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (isset($building) && $building->usage == 0) ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('usage') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="constructed_on">Constructed On</label>
                <input type="date" name="constructed_on" class="form-control" value="{{ old('constructed_on', $building->constructed_on ?? '') }}">
                @error('constructed_on') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Repairs Section -->
            <div class="form-group">
                <label>Repairs</label>
                <div id="repairs-container">
                    @if(isset($building) && $building->repairs)
                        <table class="table table-bordered" id="repairs-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Cost</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($building->repairs as $index => $repair)
                                    <tr class="repair-item">
                                        <td>
                                            <select name="repairs[{{ $index }}][building_repair_category_id]" class="form-control">
                                                @foreach($repairCategories as $category)
                                                    <option value="{{ $category->id }}" {{ $repair->building_repair_category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="repairs[{{ $index }}][description]" class="form-control" value="{{ $repair->description }}">
                                        </td>
                                        <td>
                                            <input type="number" name="repairs[{{ $index }}][cost]" class="form-control" value="{{ $repair->cost }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-repair">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <button type="button" class="btn btn-success btn-sm" id="add-repair">Add Repair</button>
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($building) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let repairsTable = document.getElementById('repairs-table').querySelector('tbody');
        let addRepairButton = document.getElementById('add-repair');
        let repairIndex = {{ isset($building) && $building->repairs ? $building->repairs->count() : 0 }};

        addRepairButton.addEventListener('click', function () {
            let repairRow = `
                <tr class="repair-item">
                    <td>
                        <select name="repairs[${repairIndex}][building_repair_category_id]" class="form-control">
                            @foreach($repairCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="repairs[${repairIndex}][description]" class="form-control">
                    </td>
                    <td>
                        <input type="number" name="repairs[${repairIndex}][cost]" class="form-control">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-repair">Remove</button>
                    </td>
                </tr>
            `;
            repairsTable.insertAdjacentHTML('beforeend', repairRow);
            repairIndex++;
        });

        repairsTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-repair')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>
@endpush
