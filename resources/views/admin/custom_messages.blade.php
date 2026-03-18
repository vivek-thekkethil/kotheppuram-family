@extends('admin.layouts.app')

@section('title', 'Custom Messages')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="card">
            <div class="card-innr">
                <h4 class="card-title mb-1">Send Custom Message</h4>
                <p class="text-light mb-4">Choose recipients, channel (Email, WhatsApp, or both), and send your message with optional attachment.</p>

                <form action="{{ route('admin.custom-messages.send') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="channel">Send via</label>
                                <select class="select-bordered select-block" id="channel" name="channel" required>
                                    <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="whatsapp" {{ old('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="both" {{ old('channel') === 'both' ? 'selected' : '' }}>Both</option>
                                </select>
                                @error('channel')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="subject">Subject</label>
                                <input class="input-bordered" type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
                                @error('subject')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="description">Description</label>
                                <textarea class="input-bordered" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                @error('description')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label" for="attachment">Attachment (optional)</label>
                                <input class="input-bordered" type="file" id="attachment" name="attachment">
                                <small class="text-light d-block mt-1">Max 10MB. For WhatsApp attachments, ensure the app is publicly reachable for Twilio media fetch.</small>
                                @error('attachment')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label d-block">Recipients</label>
                                <label class="d-flex align-items-center" style="gap:8px;">
                                    <input type="checkbox" id="send_to_all" name="send_to_all" value="1" {{ old('send_to_all') ? 'checked' : '' }}>
                                    <span>Send to all members</span>
                                </label>
                                <small class="text-light">If unchecked, select one or more members below.</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card card-bordered">
                                <div class="card-innr">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Select Recipients</h6>
                                        <button type="button" class="btn btn-sm btn-auto btn-outline" id="select_all_members">Select All Listed</button>
                                    </div>

                                    @if ($members->isEmpty())
                                        <p class="text-light mb-0">No members available.</p>
                                    @else
                                        <div class="row" id="recipient_list">
                                            @foreach ($members as $member)
                                                <div class="col-md-4 mb-2">
                                                    <label class="d-flex align-items-start" style="gap:8px;">
                                                        <input type="checkbox" name="recipient_ids[]" value="{{ $member->id }}" {{ collect(old('recipient_ids', []))->contains((string) $member->id) || collect(old('recipient_ids', []))->contains($member->id) ? 'checked' : '' }}>
                                                        <span>
                                                            <strong>{{ $member->name }}</strong><br>
                                                            <small class="text-light">{{ $member->email ?: 'No email' }} | {{ $member->phone ?: 'No phone' }}</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @error('recipient_ids')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
                                    @error('recipient_ids.*')<small class="text-danger d-block mt-2">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary" type="submit">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        var sendToAll = document.getElementById('send_to_all');
        var recipientList = document.getElementById('recipient_list');
        var selectAllButton = document.getElementById('select_all_members');

        function getRecipientCheckboxes() {
            if (!recipientList) {
                return [];
            }

            return Array.from(recipientList.querySelectorAll('input[type="checkbox"][name="recipient_ids[]"]'));
        }

        function refreshRecipientState() {
            var disabled = !!(sendToAll && sendToAll.checked);
            getRecipientCheckboxes().forEach(function (checkbox) {
                checkbox.disabled = disabled;
            });

            if (selectAllButton) {
                selectAllButton.disabled = disabled;
            }
        }

        if (sendToAll) {
            sendToAll.addEventListener('change', refreshRecipientState);
        }

        if (selectAllButton) {
            selectAllButton.addEventListener('click', function () {
                getRecipientCheckboxes().forEach(function (checkbox) {
                    if (!checkbox.disabled) {
                        checkbox.checked = true;
                    }
                });
            });
        }

        refreshRecipientState();
    })();
</script>
@endsection