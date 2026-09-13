@extends('layouts.group_member')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Title and Export Button -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex-1"></div>
        <div class="text-center flex-1">
            <h1 class="text-3xl font-bold text-gray-900 inline-flex items-center">
                <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                Investment Details
            </h1>
        </div>
        <div class="flex-1 text-right">
            <a href="{{ route('groups.member.investment-details.export', $group->group_id) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition-colors duration-200 inline-flex items-center">
                <i class="fas fa-download mr-2"></i> Export
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 hover:scale-105 transition-transform duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Investments</p>
                    <h3 class="text-xl font-bold text-gray-900">
                        BDT {{ number_format($investments->sum('amount'), 2) }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 hover:scale-105 transition-transform duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600">
                    <i class="fas fa-chart-pie text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Expected Returns</p>
                    <h3 class="text-xl font-bold text-gray-900">
                    BDT {{ number_format($investments->sum('ex_profit'), 2) }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 hover:scale-105 transition-transform duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600">
                    <i class="fas fa-hand-holding-usd text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Actual Returns</p>
                    <h3 class="text-xl font-bold text-gray-900">
                        BDT {{ number_format($investments->sum(function($investment) {
                            return $investment->returns->sum('amount');
                        }), 2) }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment History Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Investment & Return History</h2>
            <p class="text-sm text-gray-600 mt-1">Comprehensive view of all investments and their returns</p>
        </div>
        <div class="overflow-x-auto">
            @if($investments->isEmpty())
                <div class="p-6">
                    <div class="alert alert-info bg-blue-50 text-blue-700 p-4 rounded-lg">
                        No investment records found for this group.
                    </div>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Investment Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Expected Profit
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actual Returns
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Profit/Loss
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Return Date
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($investments as $investment)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                BDT {{ number_format($investment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $investment->investment_type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColor = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'active' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-blue-100 text-blue-800'
                                        ][$investment->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst($investment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                BDT {{ number_format($investment->ex_profit, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($investment->returns->isNotEmpty())
                                    BDT {{ number_format($investment->returns->sum('amount'), 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @php
                                        $actualReturns = $investment->returns->sum('amount');
                                        $profitLoss = $actualReturns - $investment->amount;
                                        $isProfit = $profitLoss >= 0;
                                    @endphp
                                    @if($investment->returns->isNotEmpty())
                                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $isProfit ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $isProfit ? 'Profit' : 'Loss' }}
                                        </span>
                                        <div class="mt-1 text-sm {{ $isProfit ? 'text-green-600' : 'text-red-600' }}">
                                            BDT {{ number_format(abs($profitLoss), 2) }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($investment->returns->isNotEmpty())
                                        {{ $investment->returns->first()->created_at->format('M d, Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection 