@extends('layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header">{{ isset($item) ? 'Edit ' .ucwords(str_replace("_", " ", Request::segment(1))) : 'Add ' .ucwords(str_replace("_", " ", Request::segment(1))) }}</h5>
    <div class="card-body">
    <form action="{{ isset($item) ? route(Request::segment(1).'.update',$item->id) : route(Request::segment(1).'.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method(isset($item) ? 'PATCH' : 'POST')
        <div class="form-group">
            <label for="name" class="col-form-label">Name</label>
            <input id="name" type="text" name="name" value="{{old('name', isset($item) ? $item->name : '')}}" class="form-control" required>
            @error('name')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="summary">Summar</label>
            <input id="summary" type="text" name="summary" value="{{old('summary', isset($item) ? $item->summary : '')}}" class="form-control" required>
            @error('summary')
                <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <div class="form-col-2">
                <label for="" class="control-label">Venue</label>
            </div>
            <div class="form-col-10">
                <select name="venue" id="venue" class="form-control form-control" required>
                    <option value="" @if(isset($item) && $item->venue==""){{"selected"}} @endif >--Select Venue--</option>
                    <option value="zone" @if(isset($item) && $item->venue=="zone"){{"selected"}} @endif >Zone</option>
                    <option value="school" @if(isset($item) && $item->venue=="school"){{"selected"}} @endif >School</option>
                </select>
                @error('venue')
                    <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <div class="form-col-2">
                <label for="" class="control-label">News Category</label>
            </div>   
            <div class="form-col-10"> 
                <select name="category_id" id="category_id" class="form-control form-control" required>
                    <option>--Select News Category--</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id}}" {{(isset($item) && $item->category_id == $category->id)  ? 'selected' : ''}}>{{$category->name}}</option>
                    @endforeach    
                </select>
                @error('category_id')
                    <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
        </div>
        @if(isset($item))
            @foreach($item->photo as $photo)
            <div class="image-container text-center">
                <img onclick="document.getElementById('fileUpload_{{$photo->id}}').click();" src="{{ asset('images/news/'.$photo->name) }}" id="saved-img{{$photo->id}}">
                <label class="container" style="font-size:12px;">
                    <input type="radio" name="sample_model" value="{{ $photo->id }}" {{ $photo->is_cover == '1' ? 'checked' : '' }}> Is Cover
                </label>
                <div class="mt-3">
                    <input type="file" class="upload-class" id="fileUpload_{{$photo->id}}" name="file[{{ $photo->id }}]" accept="image/*" style="display: none;">
                </div>
                <button type="button" class="btn btn-danger btn-sm delete-photo" data-photo-id="{{ $photo->id }}"><i class="fas fa-trash-alt"></i></button>
            </div>
            @endforeach
            {{-- Render additional image upload fields without cover selection option --}}
            @for ($i = $item->photo->count(); $i < 5; $i++)
            <div class="image-container text-center">
                <img onclick="document.getElementById('{{ 'fileUpload_' . $i }}').click();" src="{{ asset('images/news/No_Image_Available.jpg') }}" id="saved-img{{ $i }}">
                <label class="container" style="font-size:12px;">
                    <input type="radio" name="sample_model" value="{{ $i }}" disabled> Is Cover
                </label>
                <div class="mt-3">
                    <input type="file" class="upload-class" id="fileUpload_{{ $i }}" name="file[]" accept="image/*" style="display: none;">
                </div>
            </div>
            @endfor
        @else
            @for ($i = 0; $i < 5; $i++)
            <div class="image-container text-center">
                <img  onclick="document.getElementById('{{'fileUpload_'.$i}}').click();" src="{{asset('images/news/No_Image_Available.jpg')}}" id="saved-img{{$i}}">
                <label class="container" style="font-size:12px;"><input type="radio" name="sample_model[]" value="{{$i}}"> Is Cover</label>
                <div class="mt-3">
                    <input type="file" class="upload-class" id="fileUpload_{{$i}}" name="file[]" accept="image/*" style="display: none;"/>
                </div>
            </div>
            @endfor
        @endif
        <div class="form-group">
            <label for="article-content">Content</label>
            <textarea class="form-control summernote id="article-content" name="content">{{old('content', isset($item) ? $item->content : '') }}</textarea>
            @error('content')
                <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">{{ isset($item) ? 'Update': 'Add' }}</button>
        </div>
      </form>
    </div>
</div>
@endsection

@push('styles')
<style>
#uploads{
    position: relative;
    display : flex;
    justify-content:space-between;
    
}
.image-container{
    display: inline-block;
    background: #d3d3d3;
    border-radius:10px;
    width: 80px;
    height: 100px;
    margin: 3px;
    
}
.image-container img{
    width: 100%;
    height: 60px;
    border-radius:10px 10px 0px 0px;
    
}
.image-container input {
  position: relative !important;
  opacity: 100 !important;
  cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
$(document).ready(function () {
    $('.summernote').summernote({
        height: 300,
                
    });
});

$('.upload-class').click(function () {
<!--console.log(this.id.slice(this.id.indexOf('_') + 1)); -->
    var id = this.id.slice(this.id.indexOf('_') + 1);
    const imgs = document.querySelector('#saved-img' + id);
    const files = document.querySelector('#fileUpload_'+ id);
    files.addEventListener('change', function(){
        const choosedFile = this.files[0];
        if (choosedFile) {
            const readers = new FileReader(); //FileReader is a predefined function of JS
            readers.addEventListener('load', function(){
                imgs.setAttribute('src', readers.result);
            });
            readers.readAsDataURL(choosedFile);
        }
    });
});
<!--Delete news photos-->
$(document).on('click', '.delete-photo', function () {
    var photoId = $(this).data('photo-id');
    
    if (confirm('Are you sure you want to delete this photo?')) {
        $.ajax({
            url: '{{ route("news.photo.destroy", ":id") }}'.replace(':id', photoId),
            type: 'DELETE',
            data: {
                "_token": "{{ csrf_token() }}"
            },
            success: function (response) {
                alert(response.success);
                // Clear the image source without removing the container
                $('#saved-img' + photoId).attr('src', '{{ asset("images/news/No_Image_Available.jpg") }}');
            },
            error: function (response) {
                alert(response.responseJSON.error);
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form'); // Select your form
    const submitButton = document.getElementById('submit-button');

    form.addEventListener('submit', function () {
        // Disable the button
        submitButton.disabled = true;

        // Change the button text to 'Saving...'
        submitButton.innerHTML = 'Saving...';
        });
});
    
</script>
@endpush