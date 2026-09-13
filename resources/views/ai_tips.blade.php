@extends('layouts.app')

@section('title', 'AI Financial Assistant')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .glass-card {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.3);
    }
    .compact-form input, .compact-form select {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    .compact-form label {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    .compact-form .mb-4 {
        margin-bottom: 0.75rem;
    }
    #ai-response ul li {
        word-wrap: break-word;
        white-space: normal;
        font-size: 0.875rem;
        line-height: 1.4;
    }
</style>
<script>
    window.FINANCIAL_DATA = @json($financialData);
    window.CSRF_TOKEN = '{{ csrf_token() }}';
</script>

<div class="container mx-auto max-w-7xl mt-8 px-4 h-screen">
    <!-- Warning Banner - Compact -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4 rounded-md">
        <div class="flex items-center">
            <svg class="h-4 w-4 text-yellow-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.494-1.646-1.742-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <p class="text-xs text-yellow-700 ml-2">
                <strong>Caution:</strong> AI-generated financial advice is for informational purposes only. Always consult with a professional financial advisor.
            </p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-12 gap-4 h-[calc(100vh-8rem)]">
        <!-- Left Column: Overview & Form -->
        <div class="col-span-4 space-y-4">
            <!-- Savings Overview Card -->
            <div class="glass-card p-4">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Savings Overview</h3>
                <div class="space-y-2" id="savings-overview"></div>
                <div class="mt-3 pt-3 border-t">
                    <h4 class="text-sm font-semibold mb-2 text-gray-700">Group Contributions</h4>
                    <div class="space-y-1" id="group-contributions"></div>
                </div>
            </div>

            <!-- AI Guidance Form -->
            <div class="glass-card p-4 compact-form">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">AI Financial Guidance</h3>
                
                <!-- Savings Type & Group Selection -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label for="savings-type" class="block text-sm font-medium text-gray-700 mb-1">Savings Type</label>
                        <select id="savings-type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition">
                            <option value="individual">Individual</option>
                            <option value="group">Group</option>
                        </select>
                    </div>
                    <div id="group-selection" class="hidden">
                        <label for="group-id" class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                        <select id="group-id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition">
                            <option value="all">All Groups</option>
                        </select>
                    </div>
                </div>

                <!-- Question Selection -->
                <div class="mb-3">
                    <label for="question-select" class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                    <select id="question-select" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition">
                        <option value="financial_health">Financial Health</option>
                        <option value="budgeting">Budgeting</option>
                        <option value="investment_advice">Investment Options</option>
                        <option value="savings_strategy">Savings Strategy</option>
                        <option value="risk_management">Risk Management</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                <!-- Investment Options -->
                <div id="investment-options" class="mb-3">
                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <div>
                            <label for="investment-time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                            <input type="number" id="investment-time" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition" placeholder="1" value="1">
                        </div>
                        <div>
                            <label for="investment-duration" class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                            <select id="investment-duration" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition">
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                            </select>
                        </div>
                        <div>
                            <label for="investment-type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <input type="text" id="investment-type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition" placeholder="e.g., Fixed Deposit">
                        </div>
                    </div>
                </div>

                <!-- Custom Question -->
                <div id="custom-question-block" class="mb-3 hidden">
                    <label for="custom-question" class="block text-sm font-medium text-gray-700 mb-1">Your Question</label>
                    <input type="text" id="custom-question" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition" placeholder="Type your question...">
                </div>

                <!-- Submit Button -->
                <button id="get-result" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition font-semibold shadow-md text-sm">
                    Get Financial Advice
                </button>
            </div>
        </div>

        <!-- Right Column: AI Response -->
        <div class="col-span-8">
            <div class="glass-card p-4 h-full flex flex-col">
                <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">AI Financial Advice</h3>
                <div id="ai-response" class="flex-grow overflow-auto">
                    <div class="text-center text-gray-500 py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <p class="text-sm">Select your preferences and click "Get Financial Advice" to receive personalized recommendations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function displayAdvice(result) {
    const aiResponse = document.getElementById('ai-response');
    const advice = result.advice;
    
    const formattedSteps = advice.steps
        .map(step => `<li class="flex items-start text-gray-700 mb-1"><span class="mr-2 text-blue-500">•</span><span class="flex-1 text-sm">${step.replace(/^\*/, '').trim()}</span></li>`)
        .join('');
    
    aiResponse.innerHTML = `
        <div class="space-y-4 animate-fade-in h-full">
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 h-full flex flex-col">
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">${advice.title || 'Financial Advice'}</h4>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-3 mb-4">
                    <p class="text-sm text-gray-700 leading-relaxed">${advice.main_advice}</p>
                </div>
                
                <div class="flex-grow overflow-auto">
                    <h5 class="font-semibold mb-2 text-gray-700 text-sm">Action Steps:</h5>
                    <ul class="space-y-1 pl-0">
                        ${formattedSteps}
                    </ul>
                </div>
            </div>
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', () => {
    const data = window.FINANCIAL_DATA;
    
    // Savings Overview - Compact
    document.getElementById('savings-overview').innerHTML = `
        <div class="flex justify-between items-center py-1">
            <span class="text-sm text-gray-600">Total Savings</span>
            <span class="font-bold text-green-600 text-sm">৳${parseFloat(data.individual_savings).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</span>
        </div>
        <div class="flex justify-between items-center py-1">
            <span class="text-sm text-gray-600">Monthly Savings</span>
            <span class="font-bold text-blue-600 text-sm">৳${parseFloat(data.monthly_savings).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</span>
        </div>
    `;
    
    // Group Contributions - Compact
    document.getElementById('group-contributions').innerHTML = data.group_contributions.map(group => `
        <div class="flex items-center justify-between py-1">
            <span class="text-xs text-gray-600 truncate">${group.group_name}</span>
            <span class="text-xs font-bold text-indigo-600">৳${parseFloat(group.user_contribution).toLocaleString(undefined, {minimumFractionDigits:0, maximumFractionDigits:0})}</span>
        </div>
    `).join('');
    
    // Populate group selection
    const groupSelect = document.getElementById('group-id');
    if (groupSelect) {
        data.group_contributions.forEach(group => {
            const opt = document.createElement('option');
            opt.value = group.group_id;
            opt.textContent = group.group_name;
            groupSelect.appendChild(opt);
        });
    }
    
    // Show/hide group selection
    document.getElementById('savings-type').addEventListener('change', () => {
        const groupSelection = document.getElementById('group-selection');
        groupSelection.classList.toggle('hidden', document.getElementById('savings-type').value !== 'group');
    });
    
    // Show/hide custom question
    document.getElementById('question-select').addEventListener('change', function () {
        document.getElementById('custom-question-block').classList.toggle('hidden', this.value !== 'custom');
    });
    
    // Get result button
    document.getElementById('get-result').addEventListener('click', async () => {
        const questionSelect = document.getElementById('question-select');
        let question = questionSelect.value === 'custom'
            ? document.getElementById('custom-question').value.trim()
            : questionSelect.value;
            
        if (!question) {
            alert('Please select or write a question.');
            return;
        }
        
        if (question.length > 300) {
            alert('Your question is too long. Please keep it under 300 characters.');
            return;
        }
        
        question = question.replace(/[<>\n\r]/g, '');
        const aiResponse = document.getElementById('ai-response');
        
        // Show loading state
        aiResponse.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-3"></div>
                    <p class="text-sm text-gray-600">Analyzing your financial data...</p>
                </div>
            </div>
        `;
        
        const savingsType = document.getElementById('savings-type').value;
        const selectedGroupId = document.getElementById('group-id') ? document.getElementById('group-id').value : null;
        const investmentTime = document.getElementById('investment-time')?.value || null;
        const investmentDuration = document.getElementById('investment-duration')?.value || null;
        const investmentType = document.getElementById('investment-type')?.value || null;
        
        const payload = {
            savings_type: savingsType,
            group_id: selectedGroupId !== 'all' ? selectedGroupId : null,
            savings_data: data,
            question,
            investment_time: investmentTime,
            investment_duration: investmentDuration,
            investment_type: investmentType,
            all_groups_data: selectedGroupId === 'all' ? data.group_contributions : null
        };
        
        try {
            const response = await fetch('http://localhost:5000/generate_tips', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: JSON.stringify(payload)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to generate advice');
            }
            
            const result = await response.json();
            if (result.status === 'success') {
                displayAdvice(result);
            } else {
                throw new Error(result.error || 'Failed to generate advice');
            }
        } catch (err) {
            aiResponse.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Error: ${err.message}
                    </div>
                </div>
            `;
        }
    });
});
</script>
@endsection