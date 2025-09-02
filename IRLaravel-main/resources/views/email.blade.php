<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emails</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        * {
            font-size: 16px;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>

<body>
    <main class="container mt-3">
        <div>
            <input placeholder="Search" onchange="search()" id="search" class="form-control">
        </div>
        <div id="table">
            @include('email-ajax')
        </div>
    </main>
    <!-- Modal -->
    <div class="modal fade" id="detail-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <ul class="list-group">
                            <li class="list-group-item"><b>To: </b> <span id="modal-to"></span></li>
                            <li class="list-group-item"><b>Subject: </b> <span id="modal-subject"></span></li>
                        </ul>
                        <div id="modal-body" class="mt-3 border"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function search() {
            const keyword = $('#search').val()
            $.ajax({
                url: '/xemails/ajax',
                method: 'POST',
                data: {
                    keyword
                },
                success: function(resp) {
                    $('#table').html(resp.html)
                }
            })
        }
        $(function() {
            $('body').delegate('[data-action="show-detail"]', 'click', function() {
                const id = $(this).data('key')
                $.ajax({
                    url: '/xemails/show',
                    method: 'POST',
                    data: {
                        id
                    },
                    success: function(resp) {
                        $('#modal-body').html(resp.html.replace(/(?:\r\n|\r|\n)/g, '<br>'))
                        $('#modal-subject').html(resp.subject)
                        $('#modal-to').html(resp.to)
                        $('#detail-modal').modal('show')
                    }
                })
            })
        })
    </script>
</body>

</html>