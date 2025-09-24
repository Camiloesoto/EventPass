@extends('layouts.app')
@section('title','Checkout')

@section('content')
  <h2 class="text-2xl font-semibold mb-4">Checkout</h2>

  <div class="grid gap-6 md:grid-cols-3">
    <div class="md:col-span-2">
      <div class="border rounded p-4">
        <h3 class="font-semibold mb-3">Order #{{ $order->id }}</h3>
        @if ($errors->any())
          <div class="mb-3 p-2 rounded bg-red-50 text-red-700">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <ul class="divide-y">
          @foreach($order->items as $item)
            <li class="py-3 flex items-center justify-between">
              <div>
                <div class="font-medium">{{ $item->ticketType?->name }}</div>
                <div class="text-sm text-gray-600">Qty: {{ $item->quantity }}</div>
              </div>
              <div class="text-right">
                <div class="text-sm text-gray-600">Unit: ${{ number_format($item->unit_price, 2) }}</div>
                <div class="font-medium">${{ number_format($item->quantity * $item->unit_price, 2) }}</div>
              </div>
            </li>
          @endforeach
        </ul>
        <div class="flex items-center justify-end gap-6 mt-4">
          <div class="text-sm text-gray-600">Subtotal: ${{ number_format($order->subtotal_amount, 2) }}</div>
          <div class="text-sm text-gray-600">Discount: ${{ number_format($order->discount_amount, 2) }}</div>
          <div class="text-lg font-semibold">Total: ${{ number_format($order->total_amount, 2) }}</div>
        </div>
      </div>
    </div>

    <div>
      <div class="border rounded p-4">
        <h3 class="font-semibold mb-3">Payment</h3>

        @if (session('status'))
          <div class="mb-3 p-2 rounded bg-green-50 text-green-700">{{ session('status') }}</div>
        @endif

        {{-- Stripe test/fallback --}}
        <form method="POST" action="{{ route('orders.payments.store', $order) }}" class="space-y-3">
          @csrf
          <input type="hidden" name="provider" value="stripe-test" />
          <input type="hidden" name="provider_payment_id" value="test_{{ \Illuminate\Support\Str::random(12) }}" />
          <input type="hidden" name="amount" value="{{ $order->total_amount }}" />
          <input type="hidden" name="currency" value="USD" />
          <input type="hidden" name="status" value="succeeded" />

          <button type="submit" class="w-full px-5 py-2 rounded-md bg-primary text-primary-foreground hover:opacity-90 transition">
            Pay (Test)
          </button>
        </form>

        <form method="POST" action="{{ route('orders.payments.stripe.checkout', $order) }}" class="space-y-3 mt-3">
          @csrf
          <button type="submit" class="w-full px-5 py-2 rounded-md border border-border hover:bg-secondary transition">
            Pay with Stripe
          </button>
        </form>

        <p class="text-xs text-gray-500 mt-2">
          This test payment will mark the order as paid and issue tickets without charging a card.
        </p>
      </div>

      <div class="mt-4 text-sm">
        <a class="underline" href="{{ route('events.index') }}">Continue browsing</a>
      </div>
    </div>
  </div>
@endsection
