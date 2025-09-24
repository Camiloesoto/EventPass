@extends('layouts.app')
@section('title', 'EventPass')

@section('content')
  @php
    $eventCount = ($events ?? collect())->count();
    $totalSeats = ($events ?? collect())->sum(fn ($event) => $event->available_capacity);
    $nextDate = ($events ?? collect())->min(fn ($event) => optional($event->start_time)->format('M d, Y'));
  @endphp

  <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 px-10 py-20 text-white shadow-2xl shadow-slate-950/30 backdrop-blur-2xl">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-100/10 via-indigo-400/10 to-purple-400/20 opacity-70"></div>
    <div class="absolute -top-24 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full bg-white/15 blur-3xl"></div>
    <div class="relative mx-auto flex max-w-4xl flex-col items-center text-center gap-6">
      <h1 class="mt-2 text-4xl font-semibold tracking-tight sm:text-5xl">Ticketing without friction</h1>
      <p class="mt-4 max-w-3xl text-base text-white/70">
        Launch events in minutes, sell out with confidence, and welcome guests with lightning-fast check-in. EventPass keeps your team in sync from announcement to encore.
      </p>
      <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
        @guest
          <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-white/90 px-6 py-3 text-slate-900 shadow-lg shadow-indigo-500/20 transition hover:-translate-y-0.5 hover:bg-white">Start for free</a>
          <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-white/30 px-6 py-3 text-white transition hover:bg-white/10">Sign in</a>
        @endguest
        @auth
          <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-white/90 px-6 py-3 text-slate-900 shadow-lg shadow-indigo-500/20 transition hover:-translate-y-0.5 hover:bg-white">Go to dashboard</a>
        @endauth
        <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/30 px-6 py-3 text-white transition hover:bg-white/10">Browse events</a>
      </div>
      <dl class="mt-12 grid w-full gap-6 rounded-2xl border border-white/10 bg-white/5 p-6 text-left text-white/80 backdrop-blur-lg sm:grid-cols-3">
        <div>
          <dt class="text-[0.65rem] uppercase tracking-[0.25em]">Upcoming events</dt>
          <dd class="mt-2 text-2xl font-semibold text-white">{{ $eventCount }}</dd>
        </div>
        <div>
          <dt class="text-[0.65rem] uppercase tracking-[0.25em]">Open seats featured</dt>
          <dd class="mt-2 text-2xl font-semibold text-white">{{ number_format($totalSeats) }}</dd>
        </div>
        <div>
          <dt class="text-[0.65rem] uppercase tracking-[0.25em]">Next event</dt>
          <dd class="mt-2 text-2xl font-semibold text-white">{{ $nextDate ?? 'TBD' }}</dd>
        </div>
      </dl>
    </div>
  </section>

  <section class="mt-16 grid gap-6 md:grid-cols-3">
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-white/80 shadow-lg shadow-slate-950/20 backdrop-blur-sm">
      <div class="mb-4 inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 bg-white/10 text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Sell</div>
      <h2 class="text-xl font-semibold">Sell tickets in moments</h2>
      <p class="mt-2 text-sm text-white/60">Create tiers, release inventory, and watch the numbers update in real time with EventPass automations.</p>
    </div>
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-white/80 shadow-lg shadow-slate-950/20 backdrop-blur-sm">
      <div class="mb-4 inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 bg-white/10 text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Control</div>
      <h2 class="text-xl font-semibold">Control every entrance</h2>
      <p class="mt-2 text-sm text-white/60">QR passes and check-in logs keep your team synced, even across multiple doors and devices.</p>
    </div>
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-white/80 shadow-lg shadow-slate-950/20 backdrop-blur-sm">
      <div class="mb-4 inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/20 bg-white/10 text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Insights</div>
      <h2 class="text-xl font-semibold">Insights that convert</h2>
      <p class="mt-2 text-sm text-white/60">Track waitlists, sales velocity, and redemptions to fine-tune every campaign.</p>
    </div>
  </section>

  <section class="mt-20">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-semibold">Upcoming events</h2>
        <p class="text-sm text-muted-foreground">Featured happenings using EventPass.</p>
      </div>
      <a href="{{ route('events.index') }}" class="text-sm font-medium text-primary hover:underline">See all events →</a>
    </div>

    @if(($events ?? collect())->isEmpty())
      <div class="mt-8 rounded-2xl border border-dashed border-border bg-muted/30 p-10 text-center">
        <h3 class="text-lg font-semibold">No events yet</h3>
        <p class="mt-2 text-sm text-muted-foreground">Be the first to launch with EventPass and turn your idea into a sold-out experience.</p>
        <a href="{{ route('events.index') }}" class="mt-4 inline-flex items-center justify-center rounded-full bg-primary px-5 py-2 text-primary-foreground">Browse events</a>
      </div>
    @else
      <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($events as $event)
          @php
            $durationLabel = ($event->end_time && $event->start_time)
                ? $event->end_time->diffForHumans($event->start_time, true)
                : null;
          @endphp
          <article class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-6 text-white/80 shadow-lg shadow-slate-950/30 backdrop-blur transition hover:-translate-y-1 hover:shadow-2xl">
            <div class="flex items-start justify-between gap-3">
              <a href="{{ route('events.show', ['event' => $event]) }}" class="text-lg font-semibold leading-snug text-white group-hover:text-indigo-200">
                {{ $event->name }}
              </a>
              <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-medium text-indigo-100">
                {{ ucfirst(optional($event->status)->value ?? 'scheduled') }}
              </span>
            </div>
            <dl class="mt-4 space-y-2 text-sm text-white/60">
              <div class="flex items-center gap-2">
                <span class="font-medium text-white/80">Date</span>
                <span>{{ optional($event->start_time)->format('M d, Y \• h:i A') }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-medium text-white/80">Capacity</span>
                <span>{{ $event->available_capacity_text }}</span>
              </div>
            </dl>
            <div class="mt-6 flex items-center justify-between text-xs text-white/60">
              <span>{{ $durationLabel ? 'Duration '.$durationLabel : 'Updated recently' }}</span>
              <span class="font-medium text-indigo-100">View details →</span>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>
@endsection
