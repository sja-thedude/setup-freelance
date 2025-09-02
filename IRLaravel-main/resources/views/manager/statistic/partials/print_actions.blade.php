<div class="pull-right">
    <ul class="nav navbar-right panel_toolbox">
        <li>
            <a class="ir-btn ir-btn-primary print-trigger"
               data-dest="#statistic-list"
               data-has_date=".statistic-date">
                @lang('statistic.print_pdf')
            </a>
        </li>
        <li class="mgl-15">
            <a class="ir-btn ir-btn-secondary bonprinter" data-url="{!! url()->full() !!}">
                @lang('statistic.print_op_bonprinter')
            </a>
        </li>
    </ul>
</div>