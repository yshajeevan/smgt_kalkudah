@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($room) ? 'Edit Room' : 'Add Room' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($room) ? route('rooms.update', $room->id) : route('rooms.store') }}" method="POST">
            @csrf
            @if(isset($room)) @method('PUT') @endif

            <div class="form-group">
                <label for="building_id">Building</label>
                <select name="building_id" class="form-control">
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ (isset($room) && $room->building_id == $building->id) ? 'selected' : '' }}>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
                @error('building_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="room_type_id">Room Type</label>
                <select name="room_type_id" class="form-control">
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ (isset($room) && $room->room_type_id == $type->id) ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('room_type_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $room->name ?? '') }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="size">Size</label>
                <input type="text" name="size" class="form-control" value="{{ old('size', $room->size ?? '') }}">
                @error('size') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="is_available">Availability</label>
                <select name="is_available" class="form-control">
                    <option value="1" {{ (isset($room) && $room->is_available) ? 'selected' : '' }}>Available</option>
                    <option value="0" {{ (isset($room) && !$room->is_available) ? 'selected' : '' }}>Unavailable</option>
                </select>
                @error('is_available') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($room) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
