@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')
    @php
        $profileTabActive = session()->has('profile_updated') || $errors->hasAny(['name', 'mobile_number', 'date_of_birth', 'nationality']);
        $passwordTabActive = session()->has('password_updated') || $errors->hasAny(['current_password', 'password']);
        $activeTab = $passwordTabActive ? 'password' : 'personal-data';
    @endphp
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="main-content col-lg-8">
                    <div class="content-area card">
                        <div class="card-innr">
                            <div class="card-head">
                                <h4 class="card-title">Profile Details</h4>
                            </div>
                            <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab === 'personal-data' ? 'active' : '' }}" data-toggle="tab" href="#personal-data">Personal Data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#settings">Settings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" data-toggle="tab" href="#password">Password</a>
                                </li>
                            </ul><!-- .nav-tabs-line -->
                            <div class="tab-content" id="profile-details">
                                <div class="tab-pane fade {{ $activeTab === 'personal-data' ? 'show active' : '' }}" id="personal-data">
                                    <form method="POST" action="{{ route('admin.profile.update') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="full-name" class="input-item-label">Full Name</label>
                                                    <input class="input-bordered" type="text" id="full-name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                                    @error('name')
                                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                                    @enderror
                                                </div><!-- .input-item -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="email-address" class="input-item-label">Email Address</label>
                                                    <input class="input-bordered" type="text" id="email-address" name="email-address" value="{{ auth()->user()->email }}" disabled>
                                                </div><!-- .input-item -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="mobile-number" class="input-item-label">Mobile Number</label>
                                                    <input class="input-bordered" type="text" id="mobile-number" name="mobile_number" value="{{ old('mobile_number', auth()->user()->mobile_number) }}">
                                                    @error('mobile_number')
                                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                                    @enderror
                                                </div><!-- .input-item -->
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="date-of-birth" class="input-item-label">Date of Birth</label>
                                                    <input class="input-bordered date-picker-dob" type="text" id="date-of-birth" name="date_of_birth" value="{{ old('date_of_birth', optional(auth()->user()->date_of_birth)->format('Y-m-d')) }}">
                                                    @error('date_of_birth')
                                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                                    @enderror
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="nationality" class="input-item-label">Nationality</label>
                                                    <select class="select-bordered select-block" name="nationality" id="nationality">
                                                        <option value="us" {{ old('nationality', auth()->user()->nationality) === 'us' ? 'selected' : '' }}>United State</option>
                                                        <option value="uk" {{ old('nationality', auth()->user()->nationality) === 'uk' ? 'selected' : '' }}>United KingDom</option>
                                                        <option value="fr" {{ old('nationality', auth()->user()->nationality) === 'fr' ? 'selected' : '' }}>France</option>
                                                        <option value="ch" {{ old('nationality', auth()->user()->nationality) === 'ch' ? 'selected' : '' }}>China</option>
                                                        <option value="cr" {{ old('nationality', auth()->user()->nationality) === 'cr' ? 'selected' : '' }}>Czech Republic</option>
                                                        <option value="cb" {{ old('nationality', auth()->user()->nationality) === 'cb' ? 'selected' : '' }}>Colombia</option>
                                                    </select>
                                                    @error('nationality')
                                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                                    @enderror
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                        </div><!-- .row -->
                                        <div class="gaps-1x"></div><!-- 10px gap -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <button type="submit" class="btn btn-primary">Update Profile</button>
                                        </div>
                                    </form><!-- form -->
                                </div><!-- .tab-pane -->
                                <div class="tab-pane fade" id="settings">
                                    <div class="pdb-1-5x">
                                        <h5 class="card-title card-title-sm text-dark">Security Settings</h5>    
                                    </div>
                                    <div class="input-item">
                                        <input type="checkbox" class="input-switch input-switch-sm" id="save-log" checked>
                                        <label for="save-log">Save my Activities Log</label>
                                    </div>
                                    <div class="input-item">
                                        <input type="checkbox" class="input-switch input-switch-sm" id="pass-change-confirm">
                                        <label for="pass-change-confirm">Confirm me through email before password change</label>
                                    </div>
                                    <div class="pdb-1-5x">
                                        <h5 class="card-title card-title-sm text-dark">Manage Notification</h5>    
                                    </div>
                                    <div class="input-item">
                                        <input type="checkbox" class="input-switch input-switch-sm" id="latest-news" checked>
                                        <label for="latest-news">Notify me by email about sales and latest news</label>
                                    </div>
                                    <div class="input-item">
                                        <input type="checkbox" class="input-switch input-switch-sm" id="activity-alert" checked>
                                        <label for="activity-alert">Alert me by email for unusual activity.</label>
                                    </div>
                                    <div class="gaps-1x"></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span></span>
                                        <span class="text-success"><em class="ti ti-check-box"></em> Setting has been updated</span>
                                    </div>
                                </div><!-- .tab-pane -->

                                <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="password">
                                    <form method="POST" action="{{ route('admin.profile.password.update') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="current_password" class="input-item-label">Current Password</label>
                                                    <input class="input-bordered" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
                                                    @error('current_password')
                                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                                    @enderror
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                        </div><!-- .row -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="password" class="input-item-label">New Password</label>
                                                    <input class="input-bordered" type="password" id="password" name="password" autocomplete="new-password" required>
                                                    @error('password')
                                                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                                                    @enderror
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                            <div class="col-md-6">
                                                <div class="input-item input-with-label">
                                                    <label for="password_confirmation" class="input-item-label">Confirm New Password</label>
                                                    <input class="input-bordered" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                                                </div><!-- .input-item -->
                                            </div><!-- .col -->
                                        </div><!-- .row -->
                                        <div class="note note-plane note-info pdb-1x">
                                            <em class="fas fa-info-circle"></em>
                                            <p>Password should be minimum 8 letters and include lower and uppercase letters.</p>
                                        </div>
                                        <div class="gaps-1x"></div><!-- 10px gap -->
                                        <div class="d-sm-flex justify-content-between align-items-center">
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                        </div>
                                    </form>
                                </div><!-- .tab-pane -->
                            </div><!-- .tab-content -->
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                    <div class="content-area card">
                        <div class="card-innr">
                            <div class="card-head">
                                <h4 class="card-title">Two-Factor Verification</h4>
                            </div>
                            <p>Two-factor authentication is a method for protection your web account. When it is activated you need to enter not only your password, but also a special code. You can receive this code by in mobile app. Even if third person will find your password, then can't access with that code.</p>
                            <div class="d-sm-flex justify-content-between align-items-center pdt-1-5x">
                                <span class="text-light ucap d-inline-flex align-items-center">
                                    <span class="mb-0"><small>Current Status:</small></span> 
                                    <span class="badge badge-disabled ml-2">Disabled</span>
                                </span>
                                <div class="gaps-2x d-sm-none"></div>
                                <button class="order-sm-first btn btn-primary">Enable 2FA</button>
                            </div>
                        </div><!-- .card-innr -->
                    </div><!-- .card -->
                </div><!-- .col -->
                <div class="aside sidebar-right col-lg-4">
                    <div class="account-info card">
                        <div class="card-innr">
                            <h6 class="card-title card-title-sm">Your Account Status</h6>
                            <ul class="btn-grp">
                                <li><a href="#" class="btn btn-auto btn-xs btn-success">Email Verified</a></li>
                                <li><a href="#" class="btn btn-auto btn-xs btn-warning">KYC Pending</a></li>
                            </ul>
                            <div class="gaps-2-5x"></div>
                            <h6 class="card-title card-title-sm">Receiving Wallet</h6>
                            <div class="d-flex justify-content-between">
                                <span><span>0x39deb3.....e2ac64rd</span> <em class="fas fa-info-circle text-exlight" data-toggle="tooltip" data-placement="bottom" title="1 ETH = 100 TWZ"></em></span>
                                <a href="#" data-toggle="modal" data-target="#edit-wallet" class="link link-ucap">Edit</a>
                            </div>
                        </div>
                    </div>
                    <div class="referral-info card">
                        <div class="card-innr">
                            <h6 class="card-title card-title-sm">Earn with Referral</h6>
                            <p class=" pdb-0-5x">Invite your friends &amp; family and receive a <strong><span class="text-primary">bonus - 15%</span> of the value of contribution.</strong></p>
                            <div class="copy-wrap mgb-0-5x">
                                <span class="copy-feedback"></span>
                                <em class="fas fa-link"></em>
                                <input type="text" class="copy-address" value="https://demo.themenio.com/ico?ref=7d264f90653733592" disabled>
                                <button class="copy-trigger copy-clipboard" data-clipboard-text="https://demo.themenio.com/ico?ref=7d264f90653733592"><em class="ti ti-files"></em></button>
                            </div><!-- .copy-wrap -->
                        </div>
                    </div>
                    <div class="kyc-info card">
                        <div class="card-innr">
                            <h6 class="card-title card-title-sm">Identity Verification - KYC</h6>
                            <p>To comply with regulation, participant will have to go through indentity verification.</p>
                            <p class="lead text-light pdb-0-5x">You have not submitted your KYC application to verify your indentity.</p>
                            <a href="#" class="btn btn-primary btn-block">Click to Proceed</a>
                            <h6 class="kyc-alert text-danger">* KYC verification required for purchase token</h6>
                        </div>
                    </div>
                </div><!-- .col -->
            </div><!-- .container -->
        </div><!-- .container -->
    </div><!-- .page-content -->
@endsection
