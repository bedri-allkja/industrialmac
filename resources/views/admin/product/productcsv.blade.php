@extends('layouts.admin')
@section('styles')

<link href="{{asset('assets/admin/css/product.css')}}" rel="stylesheet"/>
<style>
    .import-progress-panel { display: none; margin-top: 30px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; background: #fafafa; }
    .import-progress-panel.active { display: block; }
    .import-progress-bar { height: 24px; background: #e9ecef; border-radius: 12px; overflow: hidden; margin: 16px 0; }
    .import-progress-bar-fill { height: 100%; background: linear-gradient(90deg, #2563eb, #3b82f6); transition: width 0.4s ease; text-align: center; color: #fff; font-size: 12px; line-height: 24px; min-width: 40px; }
    .import-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .import-stat-box { background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; text-align: center; }
    .import-stat-box strong { display: block; font-size: 20px; margin-bottom: 4px; }
    .import-log-box { background: #111827; color: #e5e7eb; border-radius: 6px; padding: 12px; max-height: 260px; overflow-y: auto; font-family: Consolas, monospace; font-size: 12px; white-space: pre-wrap; }
    .import-status-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .import-status-badge.running, .import-status-badge.pending { background: #dbeafe; color: #1d4ed8; }
    .import-status-badge.completed { background: #dcfce7; color: #15803d; }
    .import-status-badge.failed { background: #fee2e2; color: #b91c1c; }
    .import-server-box { margin-top: 30px; border: 2px solid #2563eb; border-radius: 8px; padding: 24px; background: #eff6ff; }
    .import-server-path { font-family: Consolas, monospace; font-size: 13px; background: #fff; border: 1px solid #cbd5e1; padding: 10px 12px; border-radius: 6px; word-break: break-all; }
    .gocover-status { position: absolute; top: 58%; left: 50%; transform: translateX(-50%); color: #fff; font-size: 16px; font-weight: 600; text-align: center; min-width: 280px; }
</style>

@endsection
@section('content')

						<div class="content-area">
							<div class="mr-breadcrumb">
								<div class="row">
									<div class="col-lg-12">
											<h4 class="heading">{{ __("Product Bulk Upload") }}</h4>
											<ul class="links">
												<li>
													<a href="{{ route('admin.dashboard') }}">{{ __("Dashboard") }} </a>
												</li>
											<li>
												<a href="javascript:;">{{ __("Products") }} </a>
											</li>
											<li>
												<a href="{{ route('admin-prod-index') }}">{{ __("All Products") }}</a>
											</li>
												<li>
													<a href="{{ route('admin-prod-import') }}">{{ __("Bulk Upload") }}</a>
												</li>
											</ul>
									</div>
								</div>
							</div>
							<div class="add-product-content">
								<div class="row">
									<div class="col-lg-12 p-5">

					                      <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
                                              <div id="gocover-status" class="gocover-status">{{ __('Working...') }}</div>
                                          </div>
					                      <form id="product-import-form" action="{{route('admin-prod-importsubmit')}}" method="POST" enctype="multipart/form-data">
					                        {{csrf_field()}}

                        						@include('alerts.admin.form-both')  

											  <div class="row">
												  <div class="col-lg-12 text-right">
													  <span style="margin-top:10px;"><a class="btn btn-primary" href="{{asset('assets/product-csv-format.csv')}}">{{ __("Download Sample CSV") }}</a></span>
												  </div>
											  </div>
											  <p class="text-muted mt-3 mb-0">
												  {{ __('Small CSV files can be uploaded below. Browser upload limit on this server:') }}
												  <strong id="php-upload-limit">...</strong>.
												  {{ __('For 450k rows, use the large file import section.') }}
											  </p>
											  <div class="row mt-3">
												  <div class="col-lg-6">
													  <label class="d-flex align-items-center">
														  <input type="checkbox" name="background" value="1" checked class="mr-2">
														  <span>{{ __('Run in background (required for large imports)') }}</span>
													  </label>
												  </div>
												  <div class="col-lg-6">
													  <label class="d-flex align-items-center">
														  <input type="checkbox" name="skip_images" value="1" checked class="mr-2">
														  <span>{{ __('Skip image download (much faster for 450k products)') }}</span>
													  </label>
												  </div>
											  </div>
											  <hr>

											  <div class="row text-center">
												  <div class="col-lg-12">
														<div class="csv-icon">
															<i class="fas fa-file-csv"></i>
														</div>
												  </div>
												  <div class="col-lg-12">
													  <div class="left-area mr-4">
														  <h4 class="heading">{{ __("Upload a File") }} *</h4>
													  </div>
													  <span class="file-btn">
														  <input type="file" id="csvfile" name="csvfile" accept=".csv">
													  </span>

												  </div>
											  </div>

						                        <input type="hidden" name="type" value="Physical">
												<div class="row">
													<div class="col-lg-12 mt-4 text-center">
														<button class="mybtn1 mr-5" type="submit">{{ __("Start Import") }}</button>
													</div>
												</div>
											</form>

                                            <div class="import-server-box">
                                                <h4 class="heading">{{ __('Large file import (450k+ rows)') }}</h4>
                                                <p class="mb-2">{{ __('Do not upload huge files through the browser. Copy your CSV here on the server:') }}</p>
                                                <div id="import-server-path" class="import-server-path">{{ storage_path('app/product-imports') }}</div>
                                                <p class="text-muted mt-3 mb-3">{{ __('Example: copy') }} <code>ersan_data_all_updated.csv</code> {{ __('into that folder, click Refresh, then Start Import.') }}</p>
                                                <div class="d-flex flex-wrap align-items-center mb-3">
                                                    <label class="d-flex align-items-center mr-4 mb-2">
                                                        <input type="checkbox" id="server-skip-images" value="1" class="mr-2">
                                                        <span>{{ __('Skip image download (strongly recommended for 450k)') }}</span>
                                                    </label>
                                                    <button type="button" id="refresh-server-files" class="mybtn1 mr-2">{{ __('Refresh file list') }}</button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered bg-white mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('File') }}</th>
                                                                <th>{{ __('Size') }}</th>
                                                                <th>{{ __('Modified') }}</th>
                                                                <th>{{ __('Action') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="server-import-files">
                                                            <tr><td colspan="4" class="text-center text-muted">{{ __('Loading files...') }}</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

											<div id="import-progress-panel" class="import-progress-panel {{ isset($activeImport) && $activeImport ? 'active' : '' }}">
												<div class="d-flex justify-content-between align-items-center flex-wrap">
													<h4 class="heading mb-0">{{ __('Import Progress') }}</h4>
													<span id="import-status-badge" class="import-status-badge {{ isset($activeImport) ? $activeImport->status : 'pending' }}">
														{{ isset($activeImport) ? $activeImport->status : 'pending' }}
													</span>
												</div>
												<p id="import-status-message" class="text-muted mt-2 mb-0">
													{{ isset($activeImport) ? $activeImport->message : __('Waiting to start...') }}
												</p>
												<div class="import-progress-bar">
													<div id="import-progress-fill" class="import-progress-bar-fill" style="width: {{ isset($activeImport) ? $activeImport->progressPercent() : 0 }}%;">
														<span id="import-progress-label">{{ isset($activeImport) ? $activeImport->progressPercent() : 0 }}%</span>
													</div>
												</div>
												<div class="import-stats">
													<div class="import-stat-box"><strong id="import-stat-processed">{{ isset($activeImport) ? number_format($activeImport->processed_rows) : 0 }}</strong><span>{{ __('Processed') }}</span></div>
													<div class="import-stat-box"><strong id="import-stat-total">{{ isset($activeImport) ? number_format($activeImport->total_rows) : 0 }}</strong><span>{{ __('Total rows') }}</span></div>
													<div class="import-stat-box"><strong id="import-stat-imported">{{ isset($activeImport) ? number_format($activeImport->imported_count) : 0 }}</strong><span>{{ __('Imported') }}</span></div>
													<div class="import-stat-box"><strong id="import-stat-skipped">{{ isset($activeImport) ? number_format($activeImport->skipped_count) : 0 }}</strong><span>{{ __('Skipped') }}</span></div>
													<div class="import-stat-box"><strong id="import-stat-errors">{{ isset($activeImport) ? number_format($activeImport->error_count) : 0 }}</strong><span>{{ __('Errors') }}</span></div>
													<div class="import-stat-box"><strong id="import-stat-speed">{{ isset($activeImport) ? $activeImport->rowsPerSecond() : 0 }}</strong><span>{{ __('Rows/sec') }}</span></div>
													<div class="import-stat-box"><strong id="import-stat-elapsed">{{ isset($activeImport) ? ($activeImport->elapsedSeconds() ?? 0) : 0 }}s</strong><span>{{ __('Elapsed') }}</span></div>
												</div>
												<h5 class="mb-2">{{ __('Import log') }}</h5>
												<div id="import-log-box" class="import-log-box">{{ isset($activeImport) ? e($activeImport->log) : '' }}</div>
											</div>
									</div>
								</div>
							</div>
						</div>



@endsection

@section('scripts')

@include('partials.admin.product.product-scripts')

<script>
(function ($) {
    var importId = {{ isset($activeImport) ? (int) $activeImport->id : 'null' }};
    var pollTimer = null;

    function setOverlayMessage(message) {
        $('#gocover-status').text(message || '{{ __('Working...') }}');
    }

    function hideOverlay() {
        if (typeof admin_loader !== 'undefined' && admin_loader == 1) {
            $('.gocover').hide();
        }
    }

    function showOverlay(message) {
        setOverlayMessage(message);
        if (typeof admin_loader !== 'undefined' && admin_loader == 1) {
            $('.gocover').show();
        }
    }

    function formatNumber(value) {
        return Number(value || 0).toLocaleString();
    }

    function updateProgressPanel(data) {
        $('#import-progress-panel').addClass('active');
        $('#import-status-badge').attr('class', 'import-status-badge ' + data.status).text(data.status);
        $('#import-status-message').text(data.message || '');
        $('#import-progress-fill').css('width', data.progress_percent + '%');
        $('#import-progress-label').text(data.total_rows > 0 ? (data.progress_percent + '%') : (data.processed_rows > 0 ? '...' : '0%'));
        $('#import-stat-processed').text(formatNumber(data.processed_rows));
        $('#import-stat-total').text(data.total_rows > 0 ? formatNumber(data.total_rows) : '—');
        $('#import-stat-imported').text(formatNumber(data.imported_count));
        $('#import-stat-skipped').text(formatNumber(data.skipped_count));
        $('#import-stat-errors').text(formatNumber(data.error_count));
        $('#import-stat-speed').text(data.rows_per_second || 0);
        $('#import-stat-elapsed').text((data.elapsed_seconds || 0) + 's');
        $('#import-log-box').text(data.log || '');
        $('#import-log-box').scrollTop($('#import-log-box')[0].scrollHeight);
    }

    function pollImportStatus() {
        if (!importId) {
            return;
        }

        $.get('{{ url('admin/products/import/status') }}/' + importId, function (data) {
            updateProgressPanel(data);

            if (data.is_running) {
                pollTimer = setTimeout(pollImportStatus, 2000);
            } else {
                var $form = $('#product-import-form');
                $form.parent().find('.alert-danger').hide();
                $form.parent().find('.alert-success').show();
                $form.parent().find('.alert-success p').html(data.message || '{{ __('Import finished.') }}');
            }
        });
    }

    if (importId) {
        pollImportStatus();
    }

    function loadServerFiles() {
        $.get('{{ route('admin-prod-import-files') }}', function (data) {
            $('#import-server-path').text(data.directory);
            $('#php-upload-limit').text(data.upload_limit + ' / post ' + data.post_limit);

            var $tbody = $('#server-import-files');
            $tbody.empty();

            if (!data.files || !data.files.length) {
                $tbody.append('<tr><td colspan="4" class="text-center text-muted">{{ __('No CSV files found in the import folder yet.') }}</td></tr>');
                return;
            }

            data.files.forEach(function (file) {
                $tbody.append(
                    '<tr>' +
                    '<td><code>' + file.name + '</code></td>' +
                    '<td>' + file.size_human + '</td>' +
                    '<td>' + file.modified + '</td>' +
                    '<td><button type="button" class="mybtn1 start-server-import" data-filename="' + file.name + '">{{ __('Start Import') }}</button></td>' +
                    '</tr>'
                );
            });
        });
    }

    loadServerFiles();
    $('#refresh-server-files').on('click', loadServerFiles);

    $(document).on('click', '.start-server-import', function () {
        var filename = $(this).data('filename');
        var $form = $('#product-import-form');

        showOverlay('{{ __('Starting background import...') }}');

        $.ajax({
            method: 'POST',
            url: '{{ route('admin-prod-import-start-file') }}',
            data: {
                _token: '{{ csrf_token() }}',
                filename: filename,
                skip_images: $('#server-skip-images').is(':checked') ? 1 : 0
            },
            complete: hideOverlay,
            success: function (data) {
                if (data.status) {
                    updateProgressPanel(data.status);
                }
                if (data.import_id) {
                    importId = data.import_id;
                }
                $form.parent().find('.alert-danger').hide();
                $form.parent().find('.alert-success').show();
                $form.parent().find('.alert-success p').html(data.message);
                pollImportStatus();
                $('html, body').animate({ scrollTop: $('#import-progress-panel').offset().top - 80 }, 300);
            },
            error: function (xhr) {
                $form.parent().find('.alert-success').hide();
                $form.parent().find('.alert-danger').show();
                var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ __('Could not start import.') }}';
                $form.parent().find('.alert-danger ul').html('<li>' + message + '</li>');
            }
        });
    });

    $('#product-import-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var formData = new FormData(this);

        if (!$form.find('[name=background]').is(':checked')) {
            formData.delete('background');
        }

        if (!$form.find('[name=skip_images]').is(':checked')) {
            formData.delete('skip_images');
        }

        showOverlay('{{ __('Uploading CSV file...') }}');

        $.ajax({
            method: 'POST',
            url: $form.prop('action'),
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            timeout: 0,
            xhr: function () {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function (event) {
                        if (event.lengthComputable) {
                            var percent = Math.round((event.loaded / event.total) * 100);
                            setOverlayMessage('{{ __('Uploading CSV file...') }} ' + percent + '%');
                        }
                    });
                }
                return xhr;
            },
            complete: hideOverlay,
            success: function (data) {
                if (data.errors) {
                    $form.parent().find('.alert-success').hide();
                    $form.parent().find('.alert-danger').show();
                    $form.parent().find('.alert-danger ul').html('');
                    for (var error in data.errors) {
                        $form.parent().find('.alert-danger ul').append('<li>' + data.errors[error] + '</li>');
                    }
                    return;
                }

                if (data.status) {
                    updateProgressPanel(data.status);
                }

                if (data.import_id) {
                    importId = data.import_id;
                }

                if (data.background) {
                    $form.parent().find('.alert-danger').hide();
                    $form.parent().find('.alert-success').show();
                    $form.parent().find('.alert-success p').html(data.message);
                    pollImportStatus();
                } else {
                    $form.parent().find('.alert-danger').hide();
                    $form.parent().find('.alert-success').show();
                    $form.parent().find('.alert-success p').html(data.message);
                }

                $(window).scrollTop(0);
            },
            error: function (xhr) {
                hideOverlay();
                $form.parent().find('.alert-success').hide();
                $form.parent().find('.alert-danger').show();

                var message = '{{ __("Import failed.") }}';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.status === 413) {
                    message = '{{ __("File too large for browser upload. Use the large file import section below.") }}';
                } else if (xhr.status === 419) {
                    message = '{{ __("Session expired. Refresh the page and try again.") }}';
                } else if (xhr.status === 0) {
                    message = '{{ __("Upload interrupted or file too large. Use the large file import section below.") }}';
                }

                $form.parent().find('.alert-danger ul').html('<li>' + message + '</li>');
            }
        });
    });
})(jQuery);
</script>
@endsection
