@extends('admin.layouts.app')

@section('title', 'Members')

@section('head')
<link rel="stylesheet" href="{{ asset('build/assets/vendor/cropper/cropper.min.css') }}">
@endsection

@section('content')
<style>
    .family-flow {
        overflow: auto;
        padding: 16px;
        width: 100%;
        min-height: 520px;
        max-height: 78vh;
        border-radius: 18px;
        background: linear-gradient(180deg, #fcfcff 0%, #f5f7ff 100%);
        border: 1px solid rgba(122, 92, 255, 0.08);
    }

    .family-tree,
    .family-tree ul {
        margin: 0;
        padding: 0;
        list-style: none;
        text-align: center;
    }

    .family-tree {
        display: inline-block;
        min-width: max-content;
        white-space: nowrap;
        padding: 8px 14px 10px;
    }

    .family-tree li {
        position: relative;
        display: inline-block;
        vertical-align: top;
        padding: 18px 10px 0;
        white-space: normal;
    }

    .family-tree li::before,
    .family-tree li::after {
        content: '';
        position: absolute;
        top: 0;
        width: 50%;
        height: 18px;
        border-top: 2px solid #d7d9ff;
    }

    .family-tree li::before {
        right: 50%;
        border-right: 2px solid #d7d9ff;
    }

    .family-tree li::after {
        left: 50%;
        border-left: 2px solid #d7d9ff;
    }

    .family-tree li:only-child::before,
    .family-tree li:only-child::after,
    .family-tree > li::before,
    .family-tree > li::after,
    .family-tree ul::before {
        display: none !important;
        content: none !important;
    }

    .family-tree li:first-child::before,
    .family-tree li:last-child::after {
        border: 0;
    }

    .family-tree li:last-child::before {
        border-right: 2px solid #d7d9ff;
        border-radius: 0 6px 0 0;
    }

    .family-tree li:first-child::after {
        border-radius: 6px 0 0 0;
    }

    .family-tree ul {
        position: relative;
        padding-top: 10px;
    }

    .family-couple {
        display: inline-flex;
        align-items: stretch;
        gap: 6px;
        position: relative;
        padding: 8px 10px;
        border-radius: 14px;
        background: linear-gradient(180deg, rgba(122, 92, 255, 0.08), rgba(255, 255, 255, 0.02));
        box-shadow: 0 10px 24px rgba(31, 38, 135, 0.08);
    }

    .family-person {
        width: 150px;
        min-width: 150px;
        max-width: 150px;
        border: 1px solid rgba(126, 87, 194, 0.14);
        border-radius: 12px;
        padding: 8px;
        text-align: left;
        transition: transform 0.2s ease;
        position: relative;
        background: linear-gradient(180deg, #ffffff 0%, #f8f9ff 100%);
        box-shadow: 0 10px 24px rgba(111, 118, 255, 0.12);
    }

    .family-person:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(111, 118, 255, 0.18);
    }

    .family-person .photo {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 6px;
        transition: transform 0.2s ease;
        border: 2px solid #fff;
        box-shadow: 0 4px 10px rgba(100, 90, 255, 0.18);
    }

    .family-person .photo-placeholder {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 6px;
        border: 1px dashed rgba(126, 87, 194, 0.35);
        transition: transform 0.2s ease;
        background: linear-gradient(180deg, #f7f1ff 0%, #eef4ff 100%);
        color: #7a5cff;
    }

    .family-person:hover .photo,
    .family-person:hover .photo-placeholder {
        transform: scale(1.18);
    }

    .family-person .relation-hover {
        display: none;
        margin-top: 6px;
        padding: 6px 8px;
        border-radius: 10px;
        background: linear-gradient(90deg, rgba(255, 94, 125, 0.1), rgba(122, 92, 255, 0.08));
        color: #6c4ed9 !important;
    }

    .family-person:hover .relation-hover {
        display: block;
    }

    .family-person .name {
        font-weight: 600;
        line-height: 1.2;
        color: #2c2c54;
        font-size: 11px;
    }

    .family-person .meta {
        font-size: 9px;
        line-height: 1.2;
    }

    .family-couple-link {
        position: relative;
        width: 18px;
        align-self: center;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .family-couple-link::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        border-top: 2px solid #ff94b6;
    }

    .family-love-icon {
        position: relative;
        z-index: 1;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff6b8b 0%, #ff9eb5 100%);
        color: #fff;
        box-shadow: 0 6px 14px rgba(255, 107, 139, 0.28);
        font-size: 7px;
    }

    .members-table {
        min-width: 980px;
        margin-bottom: 0;
    }

    .members-table th,
    .members-table td {
        padding: 12px 14px;
        vertical-align: middle;
    }

    .members-table th {
        white-space: nowrap;
    }

    .members-table td {
        line-height: 1.4;
    }

    .members-table .btn-sm {
        padding: 6px 10px;
    }

    .members-table form.d-inline {
        margin-left: 6px;
    }

    .crop-preview-thumb {
        display: none;
        width: 64px;
        height: 64px;
        margin-top: 10px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(122, 92, 255, 0.15);
        box-shadow: 0 6px 14px rgba(122, 92, 255, 0.12);
    }

    .cropper-modal-image-wrap {
        max-height: 60vh;
        overflow: hidden;
        border-radius: 12px;
        background: #f7f8ff;
    }

    #cropper-target-image {
        max-width: 100%;
        display: block;
    }
</style>
<div class="page-content">
    <div class="container">
        <div class="card">
            <div class="card-innr">
                <h4 class="card-title">Members</h4>
                <p>Add members, manage relationships, and maintain your family/member structure.</p>

                <form class="mt-4" action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="name">Member Name</label>
                                <input class="input-bordered" type="text" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="email">Email</label>
                                <input class="input-bordered" type="email" id="email" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="phone">Phone</label>
                                <input class="input-bordered" type="text" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="gender">Gender</label>
                                <select class="select-bordered select-block" id="gender" name="gender">
                                    <option value="">Select gender</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="photo">Photo</label>
                                <input class="input-bordered js-photo-crop-input" type="file" id="photo" name="photo" accept="image/*" data-hidden-target="#cropped_photo_data_create" data-preview-target="#cropped_preview_create">
                                <input type="hidden" name="cropped_photo_data" id="cropped_photo_data_create">
                                <img id="cropped_preview_create" class="crop-preview-thumb" alt="Cropped preview">
                                @error('photo')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                                @error('cropped_photo_data')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="date_of_birth">Date of Birth</label>
                                <input class="input-bordered" type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="wedding_anniversary">Wedding Anniversary</label>
                                <input class="input-bordered" type="date" id="wedding_anniversary" name="wedding_anniversary" value="{{ old('wedding_anniversary') }}">
                                @error('wedding_anniversary')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="related_member_id">Related To Member</label>
                                <select class="select-bordered select-block" id="related_member_id" name="related_member_id">
                                    <option value="">None</option>
                                    @foreach($members as $memberOption)
                                        <option value="{{ $memberOption->id }}" {{ old('related_member_id') == $memberOption->id ? 'selected' : '' }}>
                                            {{ $memberOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('related_member_id')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="relationship_to_other">Relationship Type</label>
                                <select class="select-bordered select-block" id="relationship_to_other" name="relationship_to_other">
                                    <option value="">Select relationship</option>
                                    <option value="husband" {{ old('relationship_to_other') === 'husband' ? 'selected' : '' }}>Husband</option>
                                    <option value="wife" {{ old('relationship_to_other') === 'wife' ? 'selected' : '' }}>Wife</option>
                                    <option value="son" {{ old('relationship_to_other') === 'son' ? 'selected' : '' }}>Son</option>
                                    <option value="daughter" {{ old('relationship_to_other') === 'daughter' ? 'selected' : '' }}>Daughter</option>
                                </select>
                                @error('relationship_to_other')
                                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Add Member</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-innr">
                <h5 class="card-title">Members List</h5>

                @if ($members->isEmpty())
                    <p class="text-light">No members added yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table members-table">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Wedding Anniversary</th>
                                    <th>Relation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            @if($member->photo_path)
                                                <img src="{{ asset($member->photo_path) }}" alt="{{ $member->name }}" style="width: 42px; height: 42px; object-fit: cover; border-radius: 50%;">
                                            @else
                                                <span class="badge badge-outline badge-light">No Photo</span>
                                            @endif
                                        </td>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->email ?: '-' }}</td>
                                        <td>{{ $member->phone ?: '-' }}</td>
                                        <td>{{ $member->gender ? ucfirst($member->gender) : '-' }}</td>
                                        <td>{{ $member->date_of_birth?->format('Y-m-d') ?: '-' }}</td>
                                        <td>{{ $member->wedding_anniversary?->format('Y-m-d') ?: '-' }}</td>
                                        <td>
                                            @if($member->relatedMember)
                                                {{ $member->relationship_to_other ?: 'Related' }} to {{ $member->relatedMember->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit-member-{{ $member->id }}">Update</button>
                                            <form action="{{ route('admin.members.delete', $member) }}" method="POST" class="d-inline" data-delete-confirm="true" data-delete-title="Delete Member" data-delete-message="Are you sure you want to delete this member? This action cannot be undone.">
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

        <div class="card mt-4">
            <div class="card-innr">
                <h5 class="card-title">Family Tree</h5>

                @if (empty($familyTree))
                    <p class="text-light">Family tree will appear after adding members.</p>
                @else
                    <div class="family-flow">
                        <ul class="family-tree">
                            @foreach($familyTree as $node)
                                @include('admin.partials.family-tree-node', ['node' => $node])
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        @foreach($members as $member)
            <div class="modal fade" id="edit-member-{{ $member->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Member</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Member Name</label>
                                            <input class="input-bordered" type="text" name="name" value="{{ $member->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Email</label>
                                            <input class="input-bordered" type="email" name="email" value="{{ $member->email }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Phone</label>
                                            <input class="input-bordered" type="text" name="phone" value="{{ $member->phone }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Gender</label>
                                            <select class="select-bordered select-block" name="gender">
                                                <option value="" {{ empty($member->gender) ? 'selected' : '' }}>Select gender</option>
                                                <option value="male" {{ $member->gender === 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ $member->gender === 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ $member->gender === 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Photo</label>
                                            <input class="input-bordered js-photo-crop-input" type="file" name="photo" accept="image/*" data-hidden-target="#cropped_photo_data_update_{{ $member->id }}" data-preview-target="#cropped_preview_update_{{ $member->id }}">
                                            <input type="hidden" name="cropped_photo_data" id="cropped_photo_data_update_{{ $member->id }}">
                                            <img id="cropped_preview_update_{{ $member->id }}" class="crop-preview-thumb" alt="Cropped preview">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Date of Birth</label>
                                            <input class="input-bordered" type="date" name="date_of_birth" value="{{ $member->date_of_birth?->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Wedding Anniversary</label>
                                            <input class="input-bordered" type="date" name="wedding_anniversary" value="{{ $member->wedding_anniversary?->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Related To Member</label>
                                            <select class="select-bordered select-block" name="related_member_id">
                                                <option value="">None</option>
                                                @foreach($members as $memberOption)
                                                    @if($memberOption->id !== $member->id)
                                                        <option value="{{ $memberOption->id }}" {{ $member->related_member_id === $memberOption->id ? 'selected' : '' }}>
                                                            {{ $memberOption->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-with-label">
                                            <label class="input-item-label">Relationship Type</label>
                                            <select class="select-bordered select-block" name="relationship_to_other">
                                                <option value="">Select relationship</option>
                                                <option value="husband" {{ $member->relationship_to_other === 'husband' ? 'selected' : '' }}>Husband</option>
                                                <option value="wife" {{ $member->relationship_to_other === 'wife' ? 'selected' : '' }}>Wife</option>
                                                <option value="son" {{ $member->relationship_to_other === 'son' ? 'selected' : '' }}>Son</option>
                                                <option value="daughter" {{ $member->relationship_to_other === 'daughter' ? 'selected' : '' }}>Daughter</option>
                                            </select>
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

<div class="modal fade" id="member-photo-cropper-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Member Photo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="cropper-modal-image-wrap">
                    <img id="cropper-target-image" alt="Crop photo preview">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="apply-member-photo-crop">Apply Crop</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('build/assets/vendor/cropper/cropper.min.js') }}"></script>
<script>
    (function () {
        var cropper = null;
        var activeInput = null;
        var activeHiddenInput = null;
        var activePreviewImage = null;
        var cropperImage = document.getElementById('cropper-target-image');
        var cropperModal = $('#member-photo-cropper-modal');

        document.querySelectorAll('.js-photo-crop-input').forEach(function (input) {
            input.addEventListener('change', function (event) {
                var file = event.target.files && event.target.files[0];
                if (!file) {
                    return;
                }

                activeInput = input;
                activeHiddenInput = document.querySelector(input.dataset.hiddenTarget);
                activePreviewImage = document.querySelector(input.dataset.previewTarget);

                var reader = new FileReader();
                reader.onload = function (loadEvent) {
                    cropperImage.src = loadEvent.target.result;
                    cropperModal.modal('show');
                };
                reader.readAsDataURL(file);
            });
        });

        cropperModal.on('shown.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                responsive: true,
                background: false,
            });
        });

        cropperModal.on('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        document.getElementById('apply-member-photo-crop').addEventListener('click', function () {
            if (!cropper || !activeHiddenInput) {
                return;
            }

            var canvas = cropper.getCroppedCanvas({
                width: 600,
                height: 600,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            var croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
            activeHiddenInput.value = croppedDataUrl;

            if (activePreviewImage) {
                activePreviewImage.src = croppedDataUrl;
                activePreviewImage.style.display = 'block';
            }

            cropperModal.modal('hide');
        });
    })();
</script>
@endsection
