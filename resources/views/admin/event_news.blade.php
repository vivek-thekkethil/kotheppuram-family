@extends('admin.layouts.app')

@section('title', 'Events & News')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card">
            <div class="card-innr">
                <h4 class="card-title">Events & News</h4>
                <p>Manage upcoming events and news updates with date, image, and details.</p>

                <form class="mt-4" action="{{ route('admin.event-news.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="type">Type</label>
                                <select class="select-bordered select-block" id="type" name="type" required>
                                    <option value="event" {{ old('type') === 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="news" {{ old('type') === 'news' ? 'selected' : '' }}>News</option>
                                </select>
                                @error('type')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="title">Title</label>
                                <input class="input-bordered" type="text" id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="item_date">Date</label>
                                <input class="input-bordered" type="date" id="item_date" name="item_date" value="{{ old('item_date') }}">
                                @error('item_date')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="photo">Photo</label>
                                <input class="input-bordered" type="file" id="photo" name="photo" accept="image/*">
                                @error('photo')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="description">Description</label>
                                <textarea class="input-bordered" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Add Entry</button>
                </form>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card card-full-height">
                    <div class="card-innr">
                        <h5 class="card-title">Events List</h5>

                        @if ($events->isEmpty())
                            <p class="text-light">No events added yet.</p>
                        @else
                            @foreach($events as $item)
                                <div class="card card-bordered mb-3">
                                    <div class="card-innr">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $item->title }}</h6>
                                                <small class="text-light">{{ $item->item_date?->format('Y-m-d') ?: 'No date' }}</small>
                                            </div>
                                            <span class="badge badge-primary">Event</span>
                                        </div>
                                        @if($item->photo_path)
                                            <img src="{{ asset($item->photo_path) }}" alt="{{ $item->title }}" style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                                        @endif
                                        <p class="mb-3">{{ $item->description ?: 'No description.' }}</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#edit-item-{{ $item->id }}">Update</button>
                                        <form action="{{ route('admin.event-news.delete', $item) }}" method="POST" class="d-inline" data-delete-confirm="true" data-delete-title="Delete Entry" data-delete-message="Are you sure you want to delete this entry? This action cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-full-height">
                    <div class="card-innr">
                        <h5 class="card-title">News List</h5>

                        @if ($news->isEmpty())
                            <p class="text-light">No news added yet.</p>
                        @else
                            @foreach($news as $item)
                                <div class="card card-bordered mb-3">
                                    <div class="card-innr">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $item->title }}</h6>
                                                <small class="text-light">{{ $item->item_date?->format('Y-m-d') ?: 'No date' }}</small>
                                            </div>
                                            <span class="badge badge-warning">News</span>
                                        </div>
                                        @if($item->photo_path)
                                            <img src="{{ asset($item->photo_path) }}" alt="{{ $item->title }}" style="width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                                        @endif
                                        <p class="mb-3">{{ $item->description ?: 'No description.' }}</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#edit-item-{{ $item->id }}">Update</button>
                                        <form action="{{ route('admin.event-news.delete', $item) }}" method="POST" class="d-inline" data-delete-confirm="true" data-delete-title="Delete Entry" data-delete-message="Are you sure you want to delete this entry? This action cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @foreach($items as $item)
            <div class="modal fade" id="edit-item-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update {{ ucfirst($item->type) }}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.event-news.update', $item) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Type</label>
                                            <select class="select-bordered select-block" name="type">
                                                <option value="event" {{ $item->type === 'event' ? 'selected' : '' }}>Event</option>
                                                <option value="news" {{ $item->type === 'news' ? 'selected' : '' }}>News</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Title</label>
                                            <input class="input-bordered" type="text" name="title" value="{{ $item->title }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Date</label>
                                            <input class="input-bordered" type="date" name="item_date" value="{{ $item->item_date?->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Photo</label>
                                            <input class="input-bordered" type="file" name="photo" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Description</label>
                                            <textarea class="input-bordered" name="description" rows="4">{{ $item->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
