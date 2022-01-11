@php
   if (
        (isset($model) && $model->getKey()) &&
        (!isset($startdate) || !$startdate)
    ){
        $startdate = $model->$fieldname;
    }

    if (!isset($startdate) || !$startdate) {
        $startdate = now();
    }

    // set end date default
    if (!isset($enddate) || !$enddate){
        $enddate = now();
    }

    if (!isset($selector) && (isset($fieldname) && $fieldname)){
        $selector = "input[name={$fieldname}]";
    }
@endphp

@once
    @push('css')
    <link rel="stylesheet" href="{{ asset('vendor/components/daterangepicker/daterangepicker.css') }}">
    @endpush
    @push('js')
    <script src="{{ asset('vendor/components/daterangepicker/daterangepicker.js') }}"></script>
    @endpush
@endonce

@push('js')
<script>
    $('{{ $selector }}').daterangepicker({
        timePicker: @json($timepicker ?? true),
        showDropdowns: @json($showdropdowns ?? true),
        timePicker24Hour: @json($timepicker24hour ?? true),
        singleDatePicker: @json($singledatepicker ?? false),
        startDate: "{{  $startdate?->format('d/m/Y H:i')  }}",
        endDate: "{{  $enddate->format('d/m/Y H:i') }}",
        opens: @json($opens ?? 'center'),
        drops: @json($drops ?? 'up'),
        locale: {
            format: @json(isset($timepicker) && !$timepicker ? 'DD/MM/YYYY' : 'DD/MM/YYYY HH:mm'),
            daysOfWeek: [
                'Di','Lu','Ma','Me','Je','Ve','Sa'
            ],
            applyLabel: 'Appliquer',
            cancelLabel: 'Annuler',
            monthNames: [
                'Janvier',
                'Février',
                'Mars',
                'Avril',
                'Mai',
                'Juin',
                'Juillet',
                'Août',
                'Septembre',
                'Octobre',
                'Novembre',
                'Décembre'
            ]
         }
    });
</script>
@endpush
