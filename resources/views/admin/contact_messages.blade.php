@extends('admin.layouts.app')

@section('title', 'Contact Messages')

@section('content')
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card card-full-height">
                    <div class="card-innr">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div>
                                <h4 class="card-title mb-1"><em class="ikon ikon-inbox mr-1"></em> Contact Messages</h4>
                                <p class="mb-0 text-light">Submitted from the public Contact page.</p>
                            </div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-auto btn-outline btn-primary">
                                <em class="ikon ikon-arrow-left mr-1"></em> Back to Dashboard
                            </a>
                        </div>

                        @if ($contactMessages->isEmpty())
                            <p class="text-light mb-0">No contact messages yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 170px;">Date</th>
                                            <th style="width: 170px;">Name</th>
                                            <th style="width: 220px;">Email</th>
                                            <th style="width: 220px;">Subject</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contactMessages as $message)
                                            <tr>
                                                <td>{{ $message->created_at->format('d M Y, h:i A') }}</td>
                                                <td>{{ $message->name }}</td>
                                                <td>
                                                    <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                                </td>
                                                <td>{{ $message->subject }}</td>
                                                <td style="min-width: 320px;">
                                                    <details>
                                                        <summary style="cursor:pointer; color:#9ca3af;">{{ \Illuminate\Support\Str::limit($message->message, 120) }}</summary>
                                                        <div style="white-space: pre-wrap; color:#e5e7eb; margin-top:8px;">{{ $message->message }}</div>
                                                    </details>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                                <small class="text-light">
                                    Showing {{ $contactMessages->firstItem() }} to {{ $contactMessages->lastItem() }} of {{ $contactMessages->total() }} messages
                                </small>

                                <div class="d-flex align-items-center gap-2">
                                    @if ($contactMessages->onFirstPage())
                                        <span class="btn btn-auto btn-outline disabled" aria-disabled="true">Previous</span>
                                    @else
                                        <a class="btn btn-auto btn-outline btn-primary" href="{{ $contactMessages->previousPageUrl() }}">Previous</a>
                                    @endif

                                    <span class="text-light px-2">Page {{ $contactMessages->currentPage() }} / {{ $contactMessages->lastPage() }}</span>

                                    @if ($contactMessages->hasMorePages())
                                        <a class="btn btn-auto btn-outline btn-primary" href="{{ $contactMessages->nextPageUrl() }}">Next</a>
                                    @else
                                        <span class="btn btn-auto btn-outline disabled" aria-disabled="true">Next</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
