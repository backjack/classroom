@php $_headLink()->prependStylesheet($_basePath().'/static/datatables/media/css/jquery.dataTables.min.css')->prependStylesheet($_basePath().'/static/datatables/extensions/Buttons/css/buttons.dataTables.min.css') @endphp
@php $_headScript()->prependFile($_basePath() . '/static/datatables/media/js/jquery.dataTables.min.js')->appendFile($_basePath() . '/static/datatables/media/js/dataTables.bootstrap.min.js')->appendFile($_basePath() . '/static/datatables/extensions/Buttons/js/dataTables.buttons.min.js')
->appendFile($_basePath() . '/static/datatables/extensions/Buttons/js/buttons.flash.min.js')
->appendFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js')
->appendFile($_basePath() . '/static/pdfmake/build/pdfmake.min.js')
->appendFile($_basePath() . '/static/pdfmake/build/vfs_fonts.js')
->appendFile($_basePath() . '/static/datatables/extensions/Buttons/js/buttons.html5.min.js')
->appendFile($_basePath() . '/static/datatables/extensions/Buttons/js/buttons.print.min.js')

@endphp

@yield('content')

<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            dom: 'Blfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            lengthMenu: [ [10, 25, 50,75, 100, -1], [10, 25, 50, 75, 100, "<?=__('all')?>"]  ],
            responsive: true,
            language: {
                "decimal":        "",
                "emptyTable":     "No data available in table",
                "info":           "<?=__('Showing')?> _START_ <?=__('to')?> _END_ <?=__('of')?> _TOTAL_ <?=__('entries')?>",
                "infoEmpty":      "<?=__('Showing')?> 0 to 0 <?=__('of')?> 0 <?=__('entries')?>",
                "infoFiltered":   "(<?=__('filtered-from')?>  _MAX_ <?=__('total')?> <?=__('entries')?>)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "<?=__('show')?> _MENU_ <?=__('entries')?>",
                "loadingRecords": "<?=__('loading')?>...",
                "processing":     "<?=__('processing')?>...",
                "search":         "<?=__('search')?>:",
                "zeroRecords":    "<?=__('no-matching-records')?>",
                "paginate": {
                    "first":      "<?=__('First')?>",
                    "last":       "<?=__('Last')?>",
                    "next":       "<?=__('Next')?>",
                    "previous":   "<?=__('Previous')?>"
                },
                "aria": {
                    "sortAscending":  ": <?=__('sort-ascending')?>",
                    "sortDescending": ": <?=__('sort-descending')?>"
                }
            }
        } );
    } );
</script>
