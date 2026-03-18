@extends('admin.layouts.app')

@section('title', 'Family History')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card">
            <div class="card-innr">
                <h4 class="card-title">Family History</h4>
                <p>Add or update your family story so it appears on the frontend website.</p>

                <form class="mt-4" action="{{ route('admin.family-history.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="title">Title</label>
                                <input class="input-bordered" type="text" id="title" name="title" value="{{ old('title', $history->title ?? 'Kotheppuram Family History') }}" required>
                                @error('title')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="subtitle">Subtitle</label>
                                <input class="input-bordered" type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $history->subtitle ?? '') }}" placeholder="Optional short subtitle">
                                @error('subtitle')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="content">History Content</label>
                                <textarea class="input-bordered" id="content" name="content" rows="8" required>{{ old('content', $history->content ?? '') }}</textarea>
                                @error('content')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="photo">History Image</label>
                                <input class="input-bordered" type="file" id="photo" name="photo" accept="image/*">
                                @error('photo')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">Save Family History</button>
                </form>

                @if (!empty($history?->image_path))
                    <div class="mt-4">
                        <h6 class="card-sub-title">Current Image</h6>
                        <img src="{{ asset($history->image_path) }}" alt="Family History" style="max-width: 280px; width: 100%; border-radius: 10px;">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
