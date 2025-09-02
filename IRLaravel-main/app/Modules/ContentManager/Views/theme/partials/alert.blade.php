@if (session('response'))
    <?php
    $response = session('response');
    $success = isset($response['success']) ? $response['success'] : true;
    $message = isset($response['message']) ? $response['message'] : array();
    ?>
    <div class="alert {{$success ? "alert-success" : "alert-danger"}}">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        @foreach($message as $msg)
            <p><i class="fa fa-check"></i> {{ $msg }}</p>
        @endforeach
    </div>
@endif