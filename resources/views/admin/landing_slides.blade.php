@extends('admin.layouts.app')

@section('title', 'Landing Slides')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card">
            <div class="card-innr">
                <h4 class="card-title">Landing Header Carousel</h4>
                <p>Manage carousel images, captions and sub-headers for the frontend landing page.</p>

                <form class="mt-4" action="{{ route('admin.landing-slides.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="title">Caption Title</label>
                                <input class="input-bordered" type="text" id="title" name="title" value="{{ old('title') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="subtitle">Sub Header</label>
                                <input class="input-bordered" type="text" id="subtitle" name="subtitle" value="{{ old('subtitle') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="image">Carousel Image</label>
                                <input class="input-bordered" type="file" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="sort_order">Sort Order</label>
                                <input class="input-bordered" type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label d-block">Active</label>
                                <label class="d-inline-flex align-items-center" style="gap: 8px;">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" checked>
                                    <span>Show this slide</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">Add Slide</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-innr">
                <h5 class="card-title">Existing Slides</h5>

                @if ($slides->isEmpty())
                    <p class="text-light">No landing slides added yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Caption</th>
                                    <th>Sub Header</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($slides as $slide)
                                    <tr>
                                        <td>
                                            <img src="{{ asset($slide->image_path) }}" alt="slide" style="width: 92px; height: 56px; object-fit: cover; border-radius: 8px;">
                                        </td>
                                        <td>{{ $slide->title }}</td>
                                        <td>{{ $slide->subtitle ?: '-' }}</td>
                                        <td>{{ $slide->sort_order }}</td>
                                        <td>
                                            @if($slide->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-light">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit-slide-{{ $slide->id }}">Update</button>
                                            <form action="{{ route('admin.landing-slides.delete', $slide) }}" method="POST" class="d-inline" data-delete-confirm="true" data-delete-title="Delete Slide" data-delete-message="Are you sure you want to delete this slide? This action cannot be undone.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @foreach($slides as $slide)
            <div class="modal fade" id="edit-slide-{{ $slide->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Slide</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.landing-slides.update', $slide) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Caption Title</label>
                                            <input class="input-bordered" type="text" name="title" value="{{ $slide->title }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Sub Header</label>
                                            <input class="input-bordered" type="text" name="subtitle" value="{{ $slide->subtitle }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">New Image (optional)</label>
                                            <input class="input-bordered" type="file" name="image" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Sort Order</label>
                                            <input class="input-bordered" type="number" name="sort_order" value="{{ $slide->sort_order }}" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label d-block">Active</label>
                                            <label class="d-inline-flex align-items-center" style="gap: 8px;">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" name="is_active" value="1" {{ $slide->is_active ? 'checked' : '' }}>
                                                <span>Show this slide</span>
                                            </label>
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
