@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('head')
<style>
    .member-analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
    }

    .member-donut-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .member-donut {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        position: relative;
        background: conic-gradient(#3b82f6 0% 33%, #ec4899 33% 66%, #14b8a6 66% 100%);
    }

    .member-donut::after {
        content: '';
        position: absolute;
        inset: 22px;
        border-radius: 50%;
        background: #131a2b;
    }

    .member-donut-center {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        z-index: 1;
        text-align: center;
        color: #fff;
    }

    .member-donut-center strong {
        font-size: 30px;
        line-height: 1;
    }

    .legend-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
        color: #cbd5e1;
    }

    .legend-left {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .legend-dot-male { background: #3b82f6; }
    .legend-dot-female { background: #ec4899; }
    .legend-dot-other { background: #14b8a6; }

    .metric-row {
        margin-bottom: 14px;
    }

    .metric-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #cbd5e1;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .metric-bar {
        width: 100%;
        height: 8px;
        border-radius: 8px;
        background: rgba(148, 163, 184, 0.22);
        overflow: hidden;
    }

    .metric-fill {
        height: 100%;
        border-radius: 8px;
    }

    .metric-fill-married { background: linear-gradient(90deg, #f59e0b, #f97316); }
    .metric-fill-under18 { background: linear-gradient(90deg, #6366f1, #3b82f6); }
    .metric-fill-adult { background: linear-gradient(90deg, #10b981, #14b8a6); }
    .metric-fill-senior { background: linear-gradient(90deg, #a855f7, #8b5cf6); }
    .metric-fill-email { background: linear-gradient(90deg, #0ea5e9, #0284c7); }
    .metric-fill-phone { background: linear-gradient(90deg, #22c55e, #16a34a); }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-full-height">
                            <div class="card-innr">
                                <h6 class="card-sub-title">Messages</h6>
                                <h2 class="mb-0">{{ $dashboardCounts['messages'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="card card-full-height">
                            <div class="card-innr">
                                <h6 class="card-sub-title">Gallery Photos</h6>
                                <h2 class="mb-0">{{ $dashboardCounts['gallery_photos'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-2 mb-4">
                        <div class="card card-full-height">
                            <div class="card-innr">
                                <h6 class="card-sub-title">Members</h6>
                                <h2 class="mb-0">{{ $dashboardCounts['members'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-2 mb-4">
                        <div class="card card-full-height">
                            <div class="card-innr">
                                <h6 class="card-sub-title">Events</h6>
                                <h2 class="mb-0">{{ $dashboardCounts['events'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-2 mb-4">
                        <div class="card card-full-height">
                            <div class="card-innr">
                                <h6 class="card-sub-title">News</h6>
                                <h2 class="mb-0">{{ $dashboardCounts['news'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-2">
                <div class="card card-full-height">
                    <div class="card-innr">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h4 class="card-title mb-0">Member Analytics</h4>
                            <span class="badge badge-outline badge-success">Total Members: {{ $memberStats['total_members'] }}</span>
                        </div>

                        <div class="member-analytics-grid">
                            <div class="card card-bordered">
                                <div class="card-innr">
                                    <h6 class="card-sub-title mb-3">Gender Distribution</h6>

                                    <div class="member-donut-wrap">
                                        <div
                                            class="member-donut"
                                            aria-label="Gender chart"
                                            data-male-percent="{{ $memberStats['male_percent'] }}"
                                            data-female-percent="{{ $memberStats['female_percent'] }}"
                                        >
                                            <div class="member-donut-center">
                                                <strong>{{ $memberStats['total_members'] }}</strong>
                                                <small>Members</small>
                                            </div>
                                        </div>

                                        <div style="flex: 1; min-width: 180px;">
                                            <div class="legend-row">
                                                <span class="legend-left"><span class="legend-dot legend-dot-male"></span> Male</span>
                                                <strong>{{ $memberStats['male_members'] }} ({{ $memberStats['male_percent'] }}%)</strong>
                                            </div>
                                            <div class="legend-row">
                                                <span class="legend-left"><span class="legend-dot legend-dot-female"></span> Female</span>
                                                <strong>{{ $memberStats['female_members'] }} ({{ $memberStats['female_percent'] }}%)</strong>
                                            </div>
                                            <div class="legend-row mb-0">
                                                <span class="legend-left"><span class="legend-dot legend-dot-other"></span> Other</span>
                                                <strong>{{ $memberStats['other_gender_members'] }} ({{ $memberStats['other_percent'] }}%)</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-bordered">
                                <div class="card-innr">
                                    <h6 class="card-sub-title mb-3">Family Composition</h6>

                                    <div class="metric-row">
                                        <div class="metric-header">
                                            <span>Married Members</span>
                                            <strong>{{ $memberStats['married_members'] }} ({{ $memberStats['married_percent'] }}%)</strong>
                                        </div>
                                        <div class="metric-bar"><div class="metric-fill metric-fill-married js-metric-fill" data-value="{{ $memberStats['married_percent'] }}"></div></div>
                                    </div>

                                    <div class="metric-row">
                                        <div class="metric-header">
                                            <span>Age Below 18</span>
                                            <strong>{{ $memberStats['below_18_members'] }} ({{ $memberStats['below_18_percent'] }}%)</strong>
                                        </div>
                                        <div class="metric-bar"><div class="metric-fill metric-fill-under18 js-metric-fill" data-value="{{ $memberStats['below_18_percent'] }}"></div></div>
                                    </div>

                                    <div class="metric-row">
                                        <div class="metric-header">
                                            <span>Age 18 to 59</span>
                                            <strong>{{ $memberStats['between_18_59_members'] }} ({{ $memberStats['age_18_59_percent'] }}%)</strong>
                                        </div>
                                        <div class="metric-bar"><div class="metric-fill metric-fill-adult js-metric-fill" data-value="{{ $memberStats['age_18_59_percent'] }}"></div></div>
                                    </div>

                                    <div class="metric-row mb-0">
                                        <div class="metric-header">
                                            <span>Senior Members (60+)</span>
                                            <strong>{{ $memberStats['senior_members'] }} ({{ $memberStats['senior_percent'] }}%)</strong>
                                        </div>
                                        <div class="metric-bar"><div class="metric-fill metric-fill-senior js-metric-fill" data-value="{{ $memberStats['senior_percent'] }}"></div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-bordered">
                                <div class="card-innr">
                                    <h6 class="card-sub-title mb-3">Member Data Coverage</h6>

                                    <div class="metric-row">
                                        <div class="metric-header">
                                            <span>Members with Email</span>
                                            <strong>{{ $memberStats['with_email_members'] }} ({{ $memberStats['with_email_percent'] }}%)</strong>
                                        </div>
                                        <div class="metric-bar"><div class="metric-fill metric-fill-email js-metric-fill" data-value="{{ $memberStats['with_email_percent'] }}"></div></div>
                                    </div>

                                    <div class="metric-row">
                                        <div class="metric-header">
                                            <span>Members with Phone</span>
                                            <strong>{{ $memberStats['with_phone_members'] }} ({{ $memberStats['with_phone_percent'] }}%)</strong>
                                        </div>
                                        <div class="metric-bar"><div class="metric-fill metric-fill-phone js-metric-fill" data-value="{{ $memberStats['with_phone_percent'] }}"></div></div>
                                    </div>

                                    <div class="alert alert-light mb-0" style="background: rgba(148, 163, 184, 0.08); border-color: rgba(148, 163, 184, 0.2); color: #cbd5e1;">
                                        Members without Date of Birth: <strong>{{ $memberStats['without_dob_members'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card card-full-height">
                    <div class="card-innr">
                        <h4 class="card-title">Admin Overview</h4>
                        <p>Welcome, {{ auth()->user()->name }}. Use the top menu to open each admin page separately.</p>

                        <div class="gaps-1x"></div>
                        <h6 class="card-sub-title">Upcoming Family Alerts (Next 30 Days)</h6>

                        @if ($upcomingAlerts->isEmpty())
                            <p class="text-light mb-0">No upcoming birthdays or anniversaries in the next 30 days.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>Alert Type</th>
                                            <th>Date</th>
                                            <th>Message</th>
                                            <th>Future Channels</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingAlerts as $alert)
                                            <tr>
                                                <td>{{ $alert['member']->name }}</td>
                                                <td>
                                                    @if ($alert['type'] === 'birthday')
                                                        <span class="badge badge-outline badge-info">Birthday</span>
                                                    @else
                                                        <span class="badge badge-outline badge-warning">Anniversary</span>
                                                    @endif
                                                </td>
                                                <td>{{ $alert['date']->format('d M Y') }}</td>
                                                <td>{{ $alert['message'] }}</td>
                                                <td>
                                                    <span class="badge badge-outline badge-success">Email Ready</span>
                                                    <span class="badge badge-outline badge-primary">WhatsApp Ready</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card card-full-height">
                    <div class="card-innr">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="card-sub-title mb-0">Recent Contact Messages</h6>
                            <a href="{{ route('admin.contact-messages') }}" class="btn btn-xs btn-outline btn-auto btn-primary">View All</a>
                        </div>

                        @if ($contactMessages->isEmpty())
                            <p class="text-light mb-0">No contact messages yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
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
                                                <td style="min-width:260px; max-width:360px; white-space:normal;">
                                                    {{ \Illuminate\Support\Str::limit($message->message, 180) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        var donut = document.querySelector('.member-donut');
        if (donut) {
            var malePercent = parseFloat(donut.getAttribute('data-male-percent') || '0');
            var femalePercent = parseFloat(donut.getAttribute('data-female-percent') || '0');
            var maleEnd = Math.max(0, Math.min(100, malePercent));
            var femaleEnd = Math.max(maleEnd, Math.min(100, maleEnd + femalePercent));

            donut.style.background = 'conic-gradient(#3b82f6 0% ' + maleEnd + '%, #ec4899 ' + maleEnd + '% ' + femaleEnd + '%, #14b8a6 ' + femaleEnd + '% 100%)';
        }

        document.querySelectorAll('.js-metric-fill').forEach(function (fill) {
            var value = parseFloat(fill.getAttribute('data-value') || '0');
            var width = Math.max(0, Math.min(100, value));
            fill.style.width = width + '%';
        });
    })();
</script>
@endsection
