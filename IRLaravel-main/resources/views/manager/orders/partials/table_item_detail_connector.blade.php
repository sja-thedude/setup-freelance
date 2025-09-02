@if(!empty($connectorsList))
    <div class="row" style="margin-top:0.5rem;">
        @foreach($connectorsList as $connector)
            @php
                $orderReference = null;
                if(!empty($order)):
                    if(!empty($orderReferences)):
                        foreach($orderReferences as $orderReferenceItem):
                            if(
                                !empty($order)
                                && $orderReferenceItem->provider == $connector->provider
                                && $orderReferenceItem->local_id == $order->id
                            ):
                                $orderReference = $orderReferenceItem;
                                break;
                            endif;
                        endforeach;
                    endif;
                endif;

                $statusClass = '';
                $statusIconClass = 'fa-circle-thin';

                if(!empty($orderReference)):
                    $orderReferenceStatus = $orderReference->getStatus($order->gereed);

                    if(!empty($orderReferenceStatus)):
                        $statusClass = 'text-'.$orderReferenceStatus;

                        if($orderReferenceStatus == $orderReference::STATUS_SUCCESS):
                            $statusIconClass = 'fa-check-circle';
                        endif;
                    endif;
                endif;

            @endphp
            <div class="col-sm-2 col-xs-12 text-more">
                <div class="{{ $statusClass }}">
                    @if(!empty($statusIconClass))
                        <i class="fa {{ $statusIconClass }}" aria-hidden="true"></i>
                    @endif

                    {{ \App\Models\SettingConnector::getProviders($connector->provider, false) }}
                </div>
            </div>
        @endforeach
    </div>
@endif