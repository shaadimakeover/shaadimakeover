@props(['search' => null])
<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__body">
        <div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_length" id="kt_table_1_length">
                        {{ $perPage }}
                        {{-- <label>Show 
                            <select name="kt_table_1_length" aria-controls="kt_table_1" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select> entries
                        </label> --}}
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div id="kt_table_1_filter" class="dataTables_filter">
                        {{-- <label>Search:
                            {{ $search }}
                        </label> --}}
                        {{ $search }}
                    </div>
                </div>
            </div>
            <div class="row table-responsive">
                <div class="col-sm-12">
                    <table
                        class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline"
                        id="kt_table_1" role="grid" aria-describedby="kt_table_1_info" style="width: 1115px;">
                        <thead>
                            {{ $thead }}
                        </thead>
                        <tbody>
                            {{ $tbody }}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5">
                <div class="dataTables_info" id="kt_table_1_info" role="status" aria-live="polite">
                    {{ $showingEntries }}
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers float-right" id="kt_table_1_paginate">
                    {{ $pagination }}
                </div>
            </div>
        </div>
    </div>
</div>
