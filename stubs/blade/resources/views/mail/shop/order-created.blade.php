<p>Новый заказ {{ $order->number }}</p>
<p>Покупатель: {{ $order->name }}, {{ $order->phone }}</p>
<p>Адрес: {{ $order->address }}</p>
<p>Сумма: {{ shop_money($order->total) }}</p>
<ul>
    @foreach ($order->items as $item)
        <li>{{ $item->name }} × {{ $item->qty }} — {{ shop_money($item->sum) }}</li>
    @endforeach
</ul>
