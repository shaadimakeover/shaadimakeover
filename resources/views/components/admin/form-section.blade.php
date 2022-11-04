@props(['submit'])


<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__body">
        <form wire:submit.prevent="{{ $submit }}">
            <div class="form-group validated row">
                {{ $form }}

                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <div class="row">
                            <div class="col-lg-6">
                                {{ $actions }}
                            </div>
                            <div class="col-lg-6 kt-align-right">
                            <!-- <button type="reset" class="btn btn-danger">Delete</button> -->
                            </div>


                        </div>
                    </div>
                </div>
             </div>
        </form>
</div>