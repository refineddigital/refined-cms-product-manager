@php
  $cellName = 'padding: 3px 5px;text-align: left;width:100%;';
  $cellCode = 'padding: 3px 5px;text-align: center;width:90px;min-width:90px;';
  $cellPrice = 'padding: 3px 5px;text-align: right;width:110px;min-width:112px;';
  $cellTotal = 'padding: 3px 5px;text-align: left;width:220px;min-width:222px;';

  $gst = config('products.orders.gst');
@endphp

<div style="font-family: Arial;">
  <div style="background-color: #dedede;border:1px solid #bbbbbb;text-transform:uppercase;padding: 15px;">
    <table style="width: 100%;border-collapse:collapse">
      <tr>
        <th valign="top" style="{!! $cellName !!}" width="100%"><span style="color: #111; font-size:14px">Products</span></th>
        <th valign="top" style="{!! $cellCode !!}" width="90px"><span style="color: #111; font-size:14px">Quantity</span></th>
        <th valign="top" style="{!! $cellPrice !!}" width="110px"><span style="color: #111; font-size:14px">Total</span></th>
      </tr>
    </table>
  </div>

  @if ($cart->items->count())
    <div style="padding: 15px;border-left:1px solid #bbbbbb;border-right:1px solid #bbbbbb;border-bottom:1px solid #bbbbbb;">
      <table style="width: 100%;border-collapse: collapse">
        @foreach ($cart->items as $item)
          @php
            $cell = 'border-bottom:1px solid #ddd;';
            if ($loop->last) {
                $cell = '';
            }
            if (!$loop->last) {
                $cell .= 'padding-bottom: 10px;';
            }
            if (!$loop->first) {
                $cell .= 'padding-top: 10px;';
            }
          @endphp
          <tr>
            <td valign="top" style="{!! $cellName.$cell !!}" width="100%">
              <span style="font-size:14px">
                <strong>{{ $item->product->name }}</strong>
                @if (isset($item->product->code)) - {{ $item->product->code }} @endif
                @if (isset($item->variations) && is_array($item->variations))
                  <div style="font-size: 12px;">
                    @foreach ($item->variations as $variation)
                      @if ($variation->value)
                        <div>
                          <strong>{{ $variation->name }}:</strong> {{ $variation->value }}
                        </div>
                      @endif
                    @endforeach
                  </div>
                @endif
              </span>
            </td>
            <td valign="top" style="{!! $cellCode.$cell !!}" width="90px"><span style="font-size:14px">{{ $item->quantity }}</span></td>
            <td valign="top" style="{!! $cellPrice.$cell !!}" width="110px"><span style="font-size:14px">${{ number_format($item->total, 2) }}</span></td>
          </tr>
        @endforeach
      </table>
    </div>
    @php
      $cell = 'border-bottom:1px solid #ddd;padding: 5px;';
      $cellCode = str_replace('center', 'left', $cellCode);
    @endphp
    <div>
      <table style="width: 100%;border-collapse: collapse;margin-top:10px;">
        <tr>
          <td valign="top" style="{!! $cellName !!}" width="100%">&nbsp;</td>
          <td valign="top" style="{!! $cellTotal.$cell !!}" width="220px"><span style="font-size:16px"><strong>Sub total:</strong></span></td>
          <td valign="top" style="{!! $cellPrice.$cell !!}" width="110px"><span style="font-size:16px">${{ number_format($cart->totals->sub_total, 2) }}</span></td>
        </tr>
        @if ($cart->totals->discount)
          <tr>
            <td valign="top" style="{!! $cellName !!}" width="100%">&nbsp;</td>
            <td valign="top" style="{!! $cellTotal.$cell !!}" width="220px"><span style="font-size:16px"><strong>Discount:</strong></span></td>
            <td valign="top" style="{!! $cellPrice.$cell !!}" width="110px"><span style="font-size:16px">${{ number_format($cart->totals->discount, 2) }}</span></td>
          </tr>
        @endif
        @if (isset($cart->delivery->zone->id))
          <tr>
            <td valign="top" style="{!! $cellName !!}" width="100%">&nbsp;</td>
            <td valign="top" style="{!! $cellTotal.$cell !!}" width="220px">
              <span style="font-size:16px">
                <strong>Delivery / Pickup:</strong>
                @if ($cart->delivery && $cart->delivery->zone)
                  <div>
                    {{ $cart->delivery->zone->name }}
                  </div>
                @endif
              </span>
            </td>
            <td valign="top" style="{!! $cellPrice.$cell !!}" width="110px"><span style="font-size:16px">${{ number_format($cart->totals->delivery, 2) }}</span></td>
          </tr>
        @endif
        @if (sizeof($cart->extra_fees))
          @foreach ($cart->extra_fees as $fee)
            <tr>
              <td valign="top" style="{!! $cellName !!}" width="100%">&nbsp;</td>
              <td valign="top" style="{!! $cellTotal.$cell !!}" width="220px"><span style="font-size:16px"><strong>{{ $fee->name }}:</strong></span></td>
              <td valign="top" style="{!! $cellPrice.$cell !!}" width="110px"><span style="font-size:16px">${{ number_format($fee->total, 2) }}</span></td>
            </tr>
          @endforeach
        @endif
        @if ($gst['active'])
          <tr>
            <td valign="top" style="{!! $cellName !!}" width="100%">&nbsp;</td>
            <td valign="top" style="{!! $cellTotal.$cell !!}" width="220px">
              <span style="font-size:16px">
                <strong>GST:</strong>
                @if ($gst['type'] === 'inc')
                  <small>Includes</small>
                @endif
              </span>
            </td>
            <td valign="top" style="{!! $cellPrice.$cell !!}" width="110px"><span style="font-size:16px">${{ number_format($cart->totals->gst, 2) }}</span></td>
          </tr>
        @endif
        <tr>
          <td valign="top" style="{!! $cellName !!}" width="100%">&nbsp;</td>
          <td valign="top" style="{!! $cellTotal.$cell !!}" width="220px"><span style="font-size:20px"><strong>Total:</strong></span></td>
          <td valign="top" style="{!! $cellPrice.$cell !!}" width="110px"><span style="font-size:20px"><strong>${{ number_format($cart->totals->total, 2) }}</strong></span></td>
        </tr>
      </table>

    </div>
  @endif

</div>
