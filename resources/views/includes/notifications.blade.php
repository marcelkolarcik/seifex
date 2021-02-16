{{--<div id="notifications_holder" class="col-md-12 display_none"  >--}}
    {{--<div class="card">--}}
        {{--<div class="card-header bg-danger text-light">Notifications</div>--}}
        {{--<div id="notifications" class="card-body"></div>--}}
    {{--</div>--}}
{{--</div>--}}
<!-- Modal -->
<div class="modal fade" id="notifications_holder" tabindex="-1" role="dialog" aria-labelledby="notifications_holderTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title " id="notifications_holderTitle">{{__('Notifications')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="notifications" class="card-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                <button type="button" class="btn btn-danger" onclick="location.reload();">{{__('Dismiss all')}}</button>
            </div>
        </div>
    </div>
</div>
