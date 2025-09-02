@if (session()->has('flash_notification.message'))
    @if (session()->has('flash_notification.overlay'))
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => session('flash_notification.title'),
            'body'       => session('flash_notification.message')
        ])
    @else
        <div class="row">
            <div class="col-md-12">
                <div id="show-order-mess" class="alert alert-{{ session('flash_notification.level') }}
                {{ session()->has('flash_notification.important') ? 'alert-important' : '' }}"
                >
                    <p>{!! session('flash_notification.message') !!}</p>
                </div>
            </div>
        </div>
    @endif
@endif