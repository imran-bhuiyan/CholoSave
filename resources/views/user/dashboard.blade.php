@extends('layouts.app')

@section('title', 'User Dashboard - CholoSave')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Welcome, {{ Auth::user()->name }}!</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Savings Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Your Savings</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Savings</span>
                    <span class="text-2xl font-bold text-indigo-600">$0.00</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Monthly Goal</span>
                    <span class="text-2xl font-bold text-green-600">$0.00</span>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Transactions</h2>
            <div class="space-y-4">
                <p class="text-gray-500">No recent transactions</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="#" class="bg-indigo-600 text-white rounded-lg p-4 text-center hover:bg-indigo-700 transition-colors">
                Add Savings
            </a>
            <a href="#" class="bg-green-600 text-white rounded-lg p-4 text-center hover:bg-green-700 transition-colors">
                Set Goal
            </a>
            <a href="#" class="bg-blue-600 text-white rounded-lg p-4 text-center hover:bg-blue-700 transition-colors">
                View History
            </a>
        </div>
    </div>
</div>
@endsection 