@extends('layouts.app')

@section('title', 'Create New Group - CholoSave')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .step {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
            display: none;
        }

        .step.active {
            opacity: 1;
            transform: translateY(0);
            display: block;
        }

        .progress-bar {
            transition: width 0.5s ease-out;
        }

        .content {
            flex: 1;
        }

        footer {
            background-color: #f1f5f9;
            padding: 2rem;
            text-align: center;
            margin-top: auto;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .input-error {
            border-color: #dc2626 !important;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="content container mx-auto px-4 py-8 max-w-5xl">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-8">Create Group</h1>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-8">
            <div class="progress-bar bg-green-600 h-2.5 rounded-full" style="width: 25%"></div>
        </div>

        <!-- Step Indicators -->
        <div class="flex justify-between mb-8">
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center">1</div>
                <span class="text-sm mt-1">Group Info</span>
            </div>
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">2</div>
                <span class="text-sm mt-1">Plan Details</span>
            </div>
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">3</div>
                <span class="text-sm mt-1">Payment</span>
            </div>
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">4</div>
                <span class="text-sm mt-1">Regulations</span>
            </div>
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">5</div>
                <span class="text-sm mt-1">Review</span>
            </div>
        </div>

        <form id="groupForm" class="space-y-6" method="POST" action="{{ route('groups.store') }}">
            @csrf
            <!-- Step 1: Group Information -->
            <div class="step active" id="step1">
                <h2 class="text-xl font-medium text-green-600 mb-4">Group Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                        <input type="text" name="group_name" required placeholder="Enter group name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="group_name_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" required placeholder="Enter group description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500"></textarea>
                        <div class="error-message" id="description_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Members</label>
                        <input type="number" name="members" required placeholder="Number of members" min="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="members_error"></div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md" id="nextButton1">Next</button>
                </div>
            </div>

            <!-- Step 2: Plan Details -->
            <div class="step" id="step2">
                <h2 class="text-xl font-medium text-green-600 mb-4">Plan Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">DPS Type</label>
                        <select name="dps_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select DPS Type</option>
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                        </select>
                        <div class="error-message" id="dps_type_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
                        <input type="number" name="time_period" required placeholder="Enter time period (months)" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="time_period_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" name="amount" required placeholder="Enter amount" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="amount_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="start_date_error"></div>
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md" id="prevButton1">Previous</button>
                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md" id="nextButton2">Next</button>
                </div>
            </div>

            <!-- Step 3: Payment -->
            <div class="step" id="step3">
                <h2 class="text-xl font-medium text-green-600 mb-4">Payment Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">bKash Number</label>
                        <input type="text" name="bKash" placeholder="Enter bKash number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="bKash_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rocket Number</label>
                        <input type="text" name="Rocket" placeholder="Enter Rocket number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="Rocket_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nagad Number</label>
                        <input type="text" name="Nagad" placeholder="Enter Nagad number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="Nagad_error"></div>
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md" id="prevButton2">Previous</button>
                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md" id="nextButton3">Next</button>
                </div>
            </div>

            <!-- Step 4: Regulations -->
            <div class="step" id="step4">
                <h2 class="text-xl font-medium text-green-600 mb-4">Regulations</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Goal Amount</label>
                        <input type="number" name="goal_amount" required placeholder="Enter goal amount" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="goal_amount_error"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Fund</label>
                        <input type="number" name="emergency_fund" required placeholder="Enter emergency fund amount" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <div class="error-message" id="emergency_fund_error"></div>
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md" id="prevButton3">Previous</button>
                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md" id="nextButton4">Next</button>
                </div>
            </div>

            <!-- Step 5: Summary Review -->
            <div class="step" id="step5">
                <h2 class="text-xl font-medium text-green-600 mb-4">Review Group Details</h2>
                <div class="bg-white shadow-md rounded-lg p-6 space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-700">Group Information</h3>
                        <div id="summaryGroupInfo" class="bg-gray-50 p-3 rounded-md space-y-2">
                            <p><strong>Group Name:</strong> <span id="summaryGroupName"></span></p>
                            <p><strong>Description:</strong> <span id="summaryDescription"></span></p>
                            <p><strong>Members:</strong> <span id="summaryMembers"></span></p>
                        </div>
                        <button type="button" class="mt-2 text-blue-600 hover:underline" onclick="editStep(0)">Edit Group Info</button>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700">Plan Details</h3>
                        <div id="summaryPlanDetails" class="bg-gray-50 p-3 rounded-md space-y-2">
                            <p><strong>DPS Type:</strong> <span id="summaryDpsType"></span></p>
                            <p><strong>Time Period:</strong> <span id="summaryTimePeriod"></span></p>
                            <p><strong>Amount:</strong> <span id="summaryAmount"></span></p>
                            <p><strong>Start Date:</strong> <span id="summaryStartDate"></span></p>
                        </div>
                        <button type="button" class="mt-2 text-blue-600 hover:underline" onclick="editStep(1)">Edit Plan Details</button>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700">Payment Details</h3>
                        <div id="summaryPaymentDetails" class="bg-gray-50 p-3 rounded-md space-y-2">
                            <p><strong>bKash Number:</strong> <span id="summaryBkashNumber"></span></p>
                            <p><strong>Rocket Number:</strong> <span id="summaryRocketNumber"></span></p>
                            <p><strong>Nagad Number:</strong> <span id="summaryNagadNumber"></span></p>
                        </div>
                        <button type="button" class="mt-2 text-blue-600 hover:underline" onclick="editStep(2)">Edit Payment Details</button>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-700">Regulations</h3>
                        <div id="summaryRegulations" class="bg-gray-50 p-3 rounded-md space-y-2">
                            <p><strong>Goal Amount:</strong> <span id="summaryGoalAmount"></span></p>
                            <p><strong>Emergency Fund:</strong> <span id="summaryEmergencyFund"></span></p>
                        </div>
                        <button type="button" class="mt-2 text-blue-600 hover:underline" onclick="editStep(3)">Edit Regulations</button>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md" id="prevButton4">Previous</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md">Confirm & Submit</button>
                </div>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 0;
        const totalSteps = 5;
        const steps = document.querySelectorAll('.step');
        const nextButton1 = document.getElementById('nextButton1');
        const nextButton2 = document.getElementById('nextButton2');
        const nextButton3 = document.getElementById('nextButton3');
        const nextButton4 = document.getElementById('nextButton4');
        const prevButton1 = document.getElementById('prevButton1');
        const prevButton2 = document.getElementById('prevButton2');
        const prevButton3 = document.getElementById('prevButton3');
        const prevButton4 = document.getElementById('prevButton4');

        // Function to show error message
        function showError(inputId, message) {
            const input = document.querySelector(`[name="${inputId}"]`);
            const errorDiv = document.getElementById(`${inputId}_error`);
            input.classList.add('input-error');
            errorDiv.textContent = message;
        }

        // Function to clear error message
        function clearError(inputId) {
            const input = document.querySelector(`[name="${inputId}"]`);
            const errorDiv = document.getElementById(`${inputId}_error`);
            input.classList.remove('input-error');
            errorDiv.textContent = '';
        }

        // Function to validate a single input
        function validateInput(inputId, rules) {
            const input = document.querySelector(`[name="${inputId}"]`);
            const value = input.value.trim();

            // Clear previous error
            clearError(inputId);

            // Check if required
            if (rules.required && !value) {
                showError(inputId, 'This field is required');
                return false;
            }

            // Check if number
            if (rules.number && value && isNaN(value)) {
                showError(inputId, 'Please enter a valid number');
                return false;
            }

            // Check if positive number
            if (rules.positive && value && parseFloat(value) <= 0) {
                showError(inputId, 'Please enter a positive number');
                return false;
            }

            // Check if date is in future
            if (rules.futureDate && value) {
                const today = new Date().toISOString().split('T')[0];
                if (value < today) {
                    showError(inputId, 'Date must be today or in the future');
                    return false;
                }
            }

            return true;
        }

        function validateStep1() {
            let isValid = true;

            // Validate group name
            if (!validateInput('group_name', { required: true })) {
                isValid = false;
            }

            // Validate description
            if (!validateInput('description', { required: true })) {
                isValid = false;
            }

            // Validate members
            if (!validateInput('members', { required: true, number: true, positive: true })) {
                isValid = false;
            }

            return isValid;
        }

        function validateStep2() {
            let isValid = true;

            // Validate DPS type
            if (!validateInput('dps_type', { required: true })) {
                isValid = false;
            }

            // Validate time period
            if (!validateInput('time_period', { required: true, number: true, positive: true })) {
                isValid = false;
            }

            // Validate amount
            if (!validateInput('amount', { required: true, number: true, positive: true })) {
                isValid = false;
            }

            // Validate start date
            if (!validateInput('start_date', { required: true, futureDate: true })) {
                isValid = false;
            }

            return isValid;
        }

        function validateStep3() {
            let isValid = true;

            // Validate payment numbers (optional)
            const bkashInput = document.querySelector('[name="bKash"]');
            const rocketInput = document.querySelector('[name="Rocket"]');
            const nagadInput = document.querySelector('[name="Nagad"]');

            // At least one payment method should be provided
            if (!bkashInput.value.trim() && !rocketInput.value.trim() && !nagadInput.value.trim()) {
                showError('bKash', 'At least one payment method is required');
                isValid = false;
            }

            return isValid;
        }

        function validateStep4() {
            let isValid = true;

            // Validate goal amount
            if (!validateInput('goal_amount', { required: true, number: true, positive: true })) {
                isValid = false;
            }

            // Validate emergency fund
            if (!validateInput('emergency_fund', { required: true, number: true, positive: true })) {
                isValid = false;
            }

            return isValid;
        }

        const showStep = (step) => {
            steps.forEach((el, index) => {
                if (index === step) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });
        };

        const updateProgressBar = () => {
            const progressBar = document.querySelector('.progress-bar');
            progressBar.style.width = ((currentStep + 1) / totalSteps) * 100 + '%';

            // Update step indicator colors
            const stepIndicators = document.querySelectorAll('.step-indicator div');
            stepIndicators.forEach((indicator, index) => {
                if (index <= currentStep) {
                    indicator.classList.remove('bg-gray-300', 'text-gray-600');
                    indicator.classList.add('bg-green-600', 'text-white');
                } else {
                    indicator.classList.remove('bg-green-600', 'text-white');
                    indicator.classList.add('bg-gray-300', 'text-gray-600');
                }
            });
        };

        // Navigation button event listeners
        nextButton1.addEventListener('click', () => {
            if (validateStep1()) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        nextButton2.addEventListener('click', () => {
            if (validateStep2()) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        nextButton3.addEventListener('click', () => {
            if (validateStep3()) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        nextButton4.addEventListener('click', () => {
            if (validateStep4()) {
                updateSummary();
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        // Previous button event listeners
        prevButton1.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        prevButton2.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        prevButton3.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        prevButton4.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        // Function to update summary
        function updateSummary() {
            document.getElementById('summaryGroupName').textContent = document.querySelector('input[name="group_name"]').value;
            document.getElementById('summaryDescription').textContent = document.querySelector('textarea[name="description"]').value;
            document.getElementById('summaryMembers').textContent = document.querySelector('input[name="members"]').value;
            document.getElementById('summaryDpsType').textContent = document.querySelector('select[name="dps_type"]').value;
            document.getElementById('summaryTimePeriod').textContent = document.querySelector('input[name="time_period"]').value + ' months';
            document.getElementById('summaryAmount').textContent = 'BDT ' + document.querySelector('input[name="amount"]').value;
            document.getElementById('summaryStartDate').textContent = document.querySelector('input[name="start_date"]').value;
            document.getElementById('summaryBkashNumber').textContent = document.querySelector('input[name="bKash"]').value || 'Not provided';
            document.getElementById('summaryRocketNumber').textContent = document.querySelector('input[name="Rocket"]').value || 'Not provided';
            document.getElementById('summaryNagadNumber').textContent = document.querySelector('input[name="Nagad"]').value || 'Not provided';
            document.getElementById('summaryGoalAmount').textContent = 'BDT ' + document.querySelector('input[name="goal_amount"]').value;
            document.getElementById('summaryEmergencyFund').textContent = 'BDT ' + document.querySelector('input[name="emergency_fund"]').value;
        }

        // Function to edit a specific step
        window.editStep = function(step) {
            currentStep = step;
            showStep(currentStep);
            updateProgressBar();
        }

        // Initialize first step
        showStep(currentStep);

        // Add input event listeners to clear errors when user starts typing
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', () => {
                clearError(input.name);
            });
        });
    });
    </script>
</body>
</html>
@endsection 