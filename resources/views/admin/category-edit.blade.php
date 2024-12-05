@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <h3>Edit Category</h3><br><br>

        <div class="wg-box">
            <form action="{{ route('admin.category.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $category->id }}">
                <fieldset class="form-field">
                    <div class="field-label">Category Name <span>*</span></div>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="form-input">
                    @error('name')
                    <span class="alert alert-danger">{{ $message }}</span>
                    @enderror
                </fieldset>

                <fieldset class="form-field">
                    <div class="field-label">Category Slug <span>*</span></div>
                    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required class="form-input">
                    @error('slug')
                    <span class="alert alert-danger">{{ $message }}</span>
                    @enderror
                </fieldset>

                <fieldset class="form-field">
                    <div class="field-label">Upload Image <span>*</span></div>
                    <div id="dropzone" class="dropzone-container" onclick="document.getElementById('myFile').click()">
                        <div id="imgpreview" class="image-preview">
                            @if(isset($category) && $category->image)
                            <img src="{{ asset('uploads/categories/' . $category->image) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Preview">
                            @else
                            <span class="dropzone-text">Click to upload an image</span>
                            @endif
                        </div>
                        <input type="file" id="myFile" name="image" accept="image/*" class="file-input" style="display: none;">
                    </div>
                    @error('image')
                    <span class="alert alert-danger">{{ $message }}</span>
                    @enderror
                </fieldset>

                <button type="submit" class="submit-button">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .wg-box {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .form-field {
        margin-bottom: 20px;
    }

    .field-label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .form-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .form-input:focus {
        border-color: #007bff;
        outline: none;
    }

    .dropzone-container {
        width: 100%;
        height: 150px;
        border: 2px dashed #ddd;
        border-radius: 5px;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        cursor: pointer;
        overflow: hidden;
    }

    .dropzone-container:hover {
        border-color: #007bff;
    }

    .dropzone-text {
        text-align: center;
        font-size: 14px;
        color: #007bff;
        cursor: pointer;
    }

    .file-input {
        font-size: 14px;
    }

    .image-preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .submit-button {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .submit-button:hover {
        background-color: #0056b3;
    }

    .alert {
        display: block;
        margin-top: 5px;
        padding: 10px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 5px;
        font-size: 14px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function() {
        // تحديث حقل slug تلقائيًا بناءً على الاسم
        $("input[name='name']").on("input", function() {
            const nameValue = $(this).val();
            const slugValue = nameValue.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-');
            $("input[name='slug']").val(slugValue);
        });

        document.getElementById('myFile').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imgpreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });

        $('#dropzone').on('dragover', function(e) {
            e.preventDefault();
            $(this).css('border-color', '#007bff');
        });

        $('#dropzone').on('dragleave', function() {
            $(this).css('border-color', '#ddd');
        });

        $('#dropzone').on('drop', function(e) {
            e.preventDefault();
            const files = e.originalEvent.dataTransfer.files;
            if (files.length) {
                $('#myFile')[0].files = files;
                const imgPreview = `<img src="${URL.createObjectURL(files[0])}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">`;
                $('#imgpreview').html(imgPreview).show();
            }
        });
    });
</script>
@endpush
