<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="floating-label-group border-lable-flt">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
            <input type="text"
                   placeholder="{{ __('PERÍODO DE PESQUISA') }}"
                   id="data_range" name="data_range" wire:model="data_range"
                   class="form-control form-control-sm format-font"
                   data-toggle="tooltip"
                   data-placement="top"
                   title="Informe o Período de Pesquisa">
        </div>
        <label for="label-codigo">{{ __('PERÍODO DE PESQUISA') }}</label>
    </div>
</div>
@push("styles")
    <link rel="stylesheet" type="text/css" href="{{URL::asset('css/custom-input-float.css')}}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@push("scripts")
    <script type="module" src="{{URL::asset('js/comum.js')}}"></script>
    <script type="module">
        utils.dateRangePicker(); // funciona se comum.js exportou corretamente
    </script>
@endpush
