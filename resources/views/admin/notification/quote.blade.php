		<a class="clear">{{ __('New Quote Request(s).') }}</a>
		@if(count($datas) > 0)
		<a id="quote-notf-clear" data-href="{{ route('quote-notf-clear') }}" class="clear" href="javascript:;">
			{{ __('Clear All') }}
		</a>
		<ul>
		@foreach($datas as $data)
			<li>
				<a href="{{ route('admin-quote-show', $data->quote_request_id) }}">
					<i class="fas fa-file-invoice"></i>
					{{ __('New quote request') }} #{{ $data->quote_request_id }}
					@if($data->quoteRequest && $data->quoteRequest->customer_name)
						— {{ $data->quoteRequest->customer_name }}
					@endif
				</a>
			</li>
		@endforeach
		</ul>
		@else
		<a class="clear" href="javascript:;">
			{{ __('No New Notifications.') }}
		</a>
		@endif
