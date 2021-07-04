@once
    @push('css')
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('vendor/components/datatable/datatables-bs4/css/dataTables.bootstrap4.css') }}">

        <link rel="stylesheet" href="{{ asset('vendor/components/datatable/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

    @endpush
    @push('js')
    <!-- DataTables -->
    <script src="{{ asset('vendor/components/datatable/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/components/datatable/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('vendor/components/datatable/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/components/datatable/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    @endpush
@endonce

@push('js')
    <!-- DataTables -->
    <script src="{{ asset('vendor/components/datatable/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/components/datatable/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('vendor/components/datatable/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/components/datatable/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(function () {
            $('{{ $selector ?? "#list" }}').DataTable({
                responsive: true,
                "pagingType": "full_numbers",
                "language": {
                    "sProcessing":     "Traitement en cours...",
                    "sSearch":         "Rechercher&nbsp;:",
                    "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
                    "sInfo":           "Affichage de l'&eacute;l&eacute;ment _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                    "sInfoEmpty":      "Affichage de l'&eacute;l&eacute;ment 0 &agrave; 0 sur 0 &eacute;l&eacute;ment",
                    "sInfoFiltered":   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                    "sInfoPostFix":    "",
                    "sLoadingRecords": "Chargement en cours...",
                    "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
                    "oPaginate": {
                        "sFirst":      "Premier",
                        "sPrevious":   "Pr&eacute;c&eacute;dent",
                        "sNext":       "Suivant",
                        "sLast":       "Dernier"
                    },
                    "oAria": {
                        "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                        "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
                    },
                    "select": {
                        "rows": {
                            _: "%d lignes séléctionnées",
                            0: "Aucune ligne séléctionnée",
                            1: "1 ligne séléctionnée"
                        }
                    }
                }
            })


        });
    </script>
@endpush
