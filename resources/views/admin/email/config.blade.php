@extends('layouts.admin')

@section('content')

<div class="content-area">
              <div class="mr-breadcrumb">
                <div class="row">
                  <div class="col-lg-12">
                      <h4 class="heading">{{ __('Email Configuration') }}</h4>
                    <ul class="links">
                      <li>
                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                      </li>
                      <li>
                        <a href="javascript:;">{{ __('Email Settings') }}</a>
                      </li>
                      <li>
                        <a href="{{ route('admin-mail-config') }}">{{ __('Email Configuration') }}</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="add-product-content1 add-product-content2">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                        <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
                        <form action="{{ route('admin-gs-update-mail') }}" id="geniusform" method="POST" enctype="multipart/form-data">
                          @csrf

                        @include('alerts.admin.form-both')  

                        <div class="row justify-content-center">
                            <div class="col-lg-3">
                              <div class="left-area">
                                <h4 class="heading">
                                    {{ __('SMTP') }}
                                </h4>
                              </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="action-list">
                                    <select class="process select droplinks {{ $gs->is_smtp == 1 ? 'drop-success' : 'drop-danger' }}">
                                      <option data-val="1" value="{{route('admin-gs-status',['is_smtp',1])}}" {{ $gs->is_smtp == 1 ? 'selected' : '' }}>{{ __('Activated') }}</option>
                                      <option data-val="0" value="{{route('admin-gs-status',['is_smtp',0])}}" {{ $gs->is_smtp == 0 ? 'selected' : '' }}>{{ __('Deactivated') }}</option>
                                    </select>
                                  </div>
                            </div>
                          </div>
                       

                          <div class="row justify-content-center">
                            <div class="col-lg-3">
                              <div class="left-area">
                                  <h4 class="heading">{{ __('Mail Driver') }} *
                                    </h4>
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <select name="mail_driver" class="input-field" required> 
                                <option value="smtp" {{ $gs->mail_driver == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ $gs->mail_driver == 'sendmail' ? 'selected' : '' }}>SENDMAIL</option>
                              </select>
                            </div>
                          </div>


                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Mail Host') }} *
                                  </h4>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="text" class="input-field" placeholder="{{ __('Mail Host') }}" name="mail_host" value="{{ $gs->mail_host }}" required="">
                            <p class="sub-heading mt-2">{{ __('This GoDaddy host blocks outbound SMTP to Office 365. Use localhost + port 25 for local sending.') }}</p>
                          </div>
                        </div>

                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Mail Port') }} *
                                  </h4>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="text" class="input-field" placeholder="{{ __('Mail Port') }} " name="mail_port" value="{{ $gs->mail_port }}" required="">
                          </div>
                        </div>



                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Encryption') }}
                                  </h4>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="text" class="input-field" placeholder="{{ __('Leave empty for localhost') }}" name="mail_encryption" value="{{ $gs->mail_encryption }}">
                            <p class="sub-heading mt-2">{{ __('Use tls/ssl only for external SMTP. Leave empty for localhost:25.') }}</p>
                          </div>
                        </div>

                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Mail Username') }}
                                  </h4>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="text" class="input-field" placeholder="{{ __('Mail Username') }} " name="mail_user" value="{{ $gs->mail_user }}">
                            <p class="sub-heading mt-2">{{ __('Not used when host is localhost (no SMTP auth).') }}</p>
                          </div>
                        </div>

                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Mail Password') }}
                                  </h4>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="password" class="input-field" placeholder="{{ __('Mail Password') }} " name="mail_pass" value="{{ $gs->mail_pass }}">
                            <p class="sub-heading mt-2">{{ __('Not used when host is localhost (no SMTP auth).') }}</p>
                          </div>
                        </div>

                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('From Email') }} *
                                  </h4>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="text" class="input-field" placeholder="{{ __('From Email') }} " name="from_email" value="{{ $gs->from_email }}" required="">
                          </div>
                        </div>

                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('From Name') }} *
                                  </h4>
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="text" class="input-field" placeholder="{{ __('From Name') }} " name="from_name" value="{{ $gs->from_name }}" required="">
                          </div>
                        </div>

                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Quote Notify Email') }}
                                  </h4>
                                <p class="sub-heading">{{ __('Personal admin inbox for new quote alerts. Separate multiple emails with commas.') }}</p>
                              </div>
                          </div>
                          <div class="col-lg-6">
                            <input type="text" class="input-field" placeholder="admin@example.com" name="quote_notify_email" value="{{ $gs->quote_notify_email }}">
                          </div>
                        </div>

                        <div class="row justify-content-center">
                          <div class="col-lg-3">
                            <div class="left-area">
                              
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <button class="addProductSubmit-btn" type="submit">{{ __('Submit') }}</button>
                          </div>
                        </div>
                     </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="add-product-content1 add-product-content2 mt-4">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                        @include('alerts.admin.form-success')
                        @include('alerts.admin.form-error')
                        @if (session('success'))
                          <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('unsuccess'))
                          <div class="alert alert-danger">{{ session('unsuccess') }}</div>
                        @endif
                        @if ($errors->any())
                          <div class="alert alert-danger">
                            <ul class="mb-0">
                              @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                              @endforeach
                            </ul>
                          </div>
                        @endif

                        <h4 class="heading mb-3">{{ __('Send Test Email') }}</h4>
                        <p class="text-muted mb-3">{{ __('Send a quick test using the SMTP settings saved above. Result is also written to Email Logs.') }}</p>

                        <form action="{{ route('admin-mail-test') }}" method="POST">
                          @csrf
                          <div class="row justify-content-center">
                            <div class="col-lg-3">
                              <div class="left-area">
                                <h4 class="heading">{{ __('Test Recipient') }} *</h4>
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <input type="email" class="input-field" name="test_email" required
                                value="{{ old('test_email', $gs->quote_notify_email ?: $gs->from_email) }}"
                                placeholder="you@example.com">
                            </div>
                          </div>
                          <div class="row justify-content-center">
                            <div class="col-lg-3">
                              <div class="left-area"></div>
                            </div>
                            <div class="col-lg-6">
                              <button class="addProductSubmit-btn" type="submit">{{ __('Send Test Email') }}</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="add-product-content1 add-product-content2 mt-4">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                        <div class="row">
                          <div class="col-lg-12">
                            <h4 class="heading mb-3">{{ __('Email Logs') }}</h4>
                            <p class="text-muted mb-3">{{ __('Latest outbound emails (sent / failed).') }}</p>
                            <div class="table-responsive">
                              <table class="table table-striped table-bordered">
                                <thead>
                                  <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('To') }}</th>
                                    <th>{{ __('From') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Error') }}</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @forelse(($emailLogs ?? []) as $log)
                                    <tr>
                                      <td>{{ $log->created_at }}</td>
                                      <td>{{ $log->type }}</td>
                                      <td>{{ $log->to_email }}</td>
                                      <td>{{ $log->from_email }}</td>
                                      <td>{{ $log->subject }}</td>
                                      <td>
                                        @if($log->status === 'sent')
                                          <span class="badge badge-success">{{ __('Sent') }}</span>
                                        @else
                                          <span class="badge badge-danger">{{ __('Failed') }}</span>
                                        @endif
                                      </td>
                                      <td style="max-width:280px;white-space:normal;">{{ $log->error }}</td>
                                    </tr>
                                  @empty
                                    <tr>
                                      <td colspan="7" class="text-center text-muted">{{ __('No email logs yet.') }}</td>
                                    </tr>
                                  @endforelse
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

@endsection