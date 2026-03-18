@extends('admin.layouts.app')

@section('title', 'Gallery')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card">
            <div class="card-innr">
                <h4 class="card-title">Gallery</h4>
                <p>Manage gallery photos.</p>

                <form class="mt-4" action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-9 col-md-8">
                            <div class="input-item input-with-label">
                                <label for="photos" class="input-item-label">Add Photos</label>
                                <input class="input-bordered" type="file" id="photos" name="photos[]" accept="image/*" multiple required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Upload Photos</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-9 col-md-8">
                            <small class="d-block mt-1 text-light">You can select multiple images.</small>
                            @error('photos')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                            @error('photos.*')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-innr">
                <h5 class="card-title">Gallery</h5>

                @if($photos->isEmpty())
                    <p class="text-light">No gallery photos yet.</p>
                @else
                    <div class="row">
                        @foreach($photos as $photo)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="card card-bordered">
                                    <div class="card-innr" style="padding: 10px;">
                                        <div class="position-relative">
                                            <img src="{{ asset($photo->path) }}" alt="Gallery Photo" class="img-fluid" style="width: 100%; height: 170px; object-fit: cover; border-radius: 6px;">
                                            <form action="{{ route('admin.gallery.delete', $photo) }}" method="POST" data-delete-confirm="true" data-delete-title="Delete Photo" data-delete-message="Are you sure you want to delete this photo? This action cannot be undone." style="position: absolute; top: 8px; right: 8px;">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-danger"
                                                    style="width: 30px; height: 30px; min-width: 30px; border-radius: 50% !important; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                                    title="Delete Photo"
                                                >
                                                    <em class="ti ti-close"></em>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $photos->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
