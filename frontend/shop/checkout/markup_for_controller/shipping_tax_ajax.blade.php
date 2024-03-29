@php
    // physical product prices along with tax
     $physical_items = Cart::content('default')->where('options.type', \App\Enums\ProductTypeEnum::PHYSICAL);
@endphp

@if(count($physical_items) > 0)
    @if(count($shipping_methods) > 0)
        <ul class="coupon-contents-details-list coupon-border">
            <h6>{{__('Shipping')}}</h6>
            @foreach($shipping_methods ?? [] as $key => $method)
                <li class="coupon-contents-details-list-item" data-country="{{$country}}" data-state="{{$state}}">
                <span class="coupon-radio-item">
                    <input type="radio" id="shipping-option-{{$method['id']}}" value="{{$method['id']}}" name="shipping_method">
                    <label for="shipping-option-{{$method['id']}}">
                        {{$method['name']}}
                    </label>
                </span>
                    <span>{{float_amount_with_currency_symbol($method['options']['cost'])}}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endif

@if(count($physical_items) > 0)
    <ul class="coupon-contents-details-list coupon-border">
        <li class="coupon-contents-details-list-item"><span> {{__('Tax (Incl)')}} </span>
            <span> {{$product_tax ? $product_tax.'%' : '0%'}} </span>
        </li>
        <li class="coupon-contents-details-list-item coupon-price"><span> {{__('Coupon Discount (-)')}} </span>
            <span>
            @php
                if (isset($coupon)) {
                    if ($coupon['discount_type'] == 'amount') {
                        $discount = site_currency_symbol().$coupon['discount'];
                    } else {
                        $discount = $coupon['discount'].'%';
                    }
                }
            @endphp

                {{isset($coupon) ? $discount : float_amount_with_currency_symbol(0.00)}}
        </span>
        </li>
        <li class="coupon-contents-details-list-item price-shipping">
            <span> {{__('Shipping Cost (+)')}} </span>
            <span> -- </span>
        </li>
    </ul>
@endif
<ul class="coupon-contents-details-list coupon-border">
    @php
        // physical product prices along with tax
        $physical_items = Cart::content('default')->where('options.type', \App\Enums\ProductTypeEnum::PHYSICAL);
        $subtotal = 0.0;
        foreach ($physical_items ?? [] as $item)
        {
            $subtotal += $item->price * $item->qty;
        }

        $taxed_price = ($subtotal * $product_tax) / 100;
        $total = $subtotal + $taxed_price;

        // digital product prices
        $digital_items = Cart::content('default')->where('options.type', \App\Enums\ProductTypeEnum::DIGITAL);
        $subtotal = 0.0;
        foreach ($digital_items ?? [] as $item)
        {
            $digital_product = \Modules\DigitalProduct\Entities\DigitalProduct::find($item->id);
            $taxed_price = 0.0;
            if (!is_null($digital_product->tax))
            {
                $tax = $digital_product?->getTax?->tax_percentage;
                $taxed_price = ($item->price * $tax) / 100;
            }
            $subtotal += $item->price + $taxed_price;
        }

        $total += $subtotal;
    @endphp
    <li class="coupon-contents-details-list-item price-total" data-total="{{$total}}">
        <h6 class="coupon-title"> {{__('Total Amount')}} </h6> <span
            class="coupon-price fw-500 color-heading"> {{float_amount_with_currency_symbol($total)}} </span>
    </li>
</ul>
