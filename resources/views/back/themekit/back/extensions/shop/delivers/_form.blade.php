<form
@if($form->getModel() && $form->getModel()->getKey())
    action="{{ back_route('extensions.shop.deliver.update', $form->getModel() ) }}"
@else
    action="{{ back_route('extensions.shop.deliver.store' ) }}"
@endif
@submit="$refs.area_prices.value = JSON.stringify(form.area_prices)"
x-data="DeliverZone()" x-init="init()"
method="post" accept-charset="UTF-8" name="{{ get_form_name($form->getModel()) }}">
    @csrf
    @if($form->getModel() && $form->getModel()->getKey())
    @method('put')
    @endif
    {!! form_row($form->getField('name')) !!}
    {!! form_row($form->getField('phone_number')) !!}
    {!! form_row($form->getField('email')) !!}
    {!! form_row($form->getField('default_deliver')) !!}

<div>
    <input type="hidden" name="area_prices" x-ref="area_prices">
    <div class="form-group">
        <template x-for="(area, index) in form.area_prices">
            <div>
                <hr>
                <div class="btn-group mb-2 d-flex justify-content-end">
                    <button @click.prevent="remove(area)" type="button" class="btn btn-danger btn-xs"><i
                            class="fa fa-times"></i> </button>
                </div>
                <div class="form-group">
                    <label for="area">Zone de livraison</label>
                    <select x-model="area.id" class="form-control">
                        <option value="">Choisir la zone de livraison</option>
                        <template x-for="coverage_area in coverage_areas">
                            <option :value="coverage_area.id" x-text="coverage_area.name" :selected="coverage_area.id == area.id"></option>
                        </template>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Prix</label>
                    <input type="number" class="form-control" x-model="area.price">
                </div>
            </div>
        </template>
    </div>

    <div class="form-group" >
        <button @click.prevent="form.area_prices.push({id: null, price: null})"  class="btn btn-secondary btn-block">Ajouter une zone de
            livraison</button>
    </div>

</div>
@if (isset($edit) && $edit)
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i> Modifier</button>
</div>
@endif

@if (!isset($edit))
<div class="form-group">
    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> Enregistrer</button>
</div>
@endif

</form>


@push('js')
    <script src="{{ asset('js/vendor/alpine.min.js') }}" defer></script>
    <script>
        function DeliverZone(){
            return {
                deliver: @json($form->getModel()),
                form: {
                    area_prices: [],
                },
                init(){
                    this.form.area_prices =  this.deliver.area_prices || []
                },
                coverage_areas: @json($coverage_areas),

                remove(area){
                    this.form.area_prices = this.form.area_prices.filter(item => item.id != area.id)
                },
            }
        }
    </script>
@endpush
