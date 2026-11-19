<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Inquiries\InquiryResource;
use App\Models\Booking;
use App\Models\Inquiry;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->allows('dashboard.view') ?? false;
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        // Prices are in two currencies, so the dashboard counts bookings rather than adding up rupees.
        $thisMonth = Booking::where('created_at', '>=', now()->startOfMonth())->count();
        $lastMonth = Booking::whereBetween('created_at', [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()])->count();

        $daily = Booking::where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')->groupBy('day')->pluck('total', 'day');
        $chart = collect(range(13, 0))->map(fn ($i) => (int) ($daily[now()->subDays($i)->toDateString()] ?? 0))->all();

        $new = Booking::where('status', 'new')->count();
        $departing = Booking::whereIn('status', ['new', 'confirmed'])
            ->whereBetween('travel_date', [today(), today()->addDays(30)])->count();
        $newInquiries = Inquiry::where('status', 'new')->count();

        return [
            Stat::make('Booking requests this month', $thisMonth)
                ->description('Last month: '.$lastMonth)
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->chart($chart)
                ->color('success'),
            Stat::make('New bookings', $new)
                ->description($new ? 'Call to confirm' : 'All confirmed')
                ->icon(Heroicon::OutlinedPhone)
                ->color($new ? 'warning' : 'gray')
                ->url(BookingResource::getUrl('index', ['filters' => ['status' => ['values' => ['new']]]])),
            Stat::make('Travelling in 30 days', $departing)
                ->description('Hotels, transport and permits ready?')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color($departing ? 'info' : 'gray')
                ->url(BookingResource::getUrl('index', ['filters' => ['upcoming' => ['isActive' => true]]])),
            Stat::make('New enquiries', $newInquiries)
                ->description('Waiting for a reply')
                ->icon(Heroicon::OutlinedInbox)
                ->color($newInquiries ? 'warning' : 'gray')
                ->url(InquiryResource::getUrl('index')),
        ];
    }
}
