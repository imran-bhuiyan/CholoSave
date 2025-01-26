<?php
session_start();
require_once 'user_finance_info.php'; // Include the financial data fetching file
include '../includes/header2.php';
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user financial data
$financial_data = fetchUserFinancialData($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="assets/css/style.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Financial Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto py-10">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6">AI Financial Assistant</h1>

        <!-- Savings Display -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="col-span-1 bg-gray-50 p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Savings Summary</h2>
                <p><strong>Total Individual Savings:</strong> BDT <?= number_format($financial_data['individual_savings'], 2) ?></p>
                <p><strong>Monthly Income:</strong> BDT <?= number_format($financial_data['monthly_income'], 2) ?></p>
                <p><strong>Monthly Expenses:</strong> BDT <?= number_format($financial_data['monthly_expenses'], 2) ?></p>
                <h3 class="mt-4 font-medium">Group Contributions:</h3>
                <ul>
                    <?php foreach ($financial_data['group_contributions'] as $group): ?>
                        <li><?= htmlspecialchars($group['group_name']) ?>: BDT <?= number_format($group['total_contribution'], 2) ?> saved out of BDT <?= number_format($group['goal_amount'], 2) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- AI Assistant Section -->
            <div class="col-span-2">
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Get AI Assistance</h2>

                    <!-- Preset Questions -->
                    <div class="mt-4">
                        <label for="question-select" class="block font-medium">Choose a Question:</label>
                        <select id="question-select" class="w-full border p-2 rounded">
                            <option value="">Select a preset question</option>
                            <option value="savings_strategy">What savings strategy should I follow?</option>
                            <option value="investment_advice">What investment options should I consider?</option>
                            <option value="risk_management">How should I manage my financial risks?</option>
                            <option value="emergency_fund">How should I handle my emergency fund?</option>
                            <option value="group_savings">How can we improve our group savings?</option>
                        </select>
                    </div>

                    <!-- Custom Question -->
                    <div class="mt-4">
                        <label for="custom-question" class="block font-medium">Or Write Your Own Question:</label>
                        <input type="text" id="custom-question" class="w-full border p-2 rounded" placeholder="Type your custom question here...">
                    </div>

                    <!-- Submit Button -->
                    <button id="get-result" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full">
                        Get Financial Analysis
                    </button>

                    <div id="ai-response" class="mt-6 hidden bg-gray-50 p-4 rounded-lg shadow"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function displayAdvice(result) {
    const aiResponse = document.getElementById('ai-response');
    const advice = result.advice;

    aiResponse.innerHTML = `
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-xl font-bold mb-4">${advice.title || 'Financial Advice'}</h3>
                <p class="text-lg text-gray-700 mb-4">${advice.main_advice}</p>
                <div class="mt-4">
                    <h4 class="font-semibold mb-2">Action Steps:</h4>
                    <ul class="space-y-2">
                        ${advice.steps.map(step => `<li class="flex items-center"><span class="text-green-500 mr-2">✓</span>${step}</li>`).join('')}
                    </ul>
                </div>
            </div>
        </div>
    `;
}

async function getAIResponse() {
    try {
        const aiResponse = document.getElementById('ai-response');
        const selectedQuestion = document.getElementById('question-select').value;
        const customQuestion = document.getElementById('custom-question').value.trim();

        // Determine which question to send
        const question = customQuestion || selectedQuestion;
        if (!question) {
            aiResponse.classList.remove('hidden');
            aiResponse.innerHTML = `
                <div class="bg-red-100 border-l-4 border-red-500 p-4">
                    <p class="text-red-700">Please select or enter a question.</p>
                </div>
            `;
            return;
        }

        // Show loading state
        aiResponse.classList.remove('hidden');
        aiResponse.innerHTML = `
            <div class="flex items-center justify-center p-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <span class="ml-2">Analyzing financial data...</span>
            </div>
        `;

        // Get financial data from PHP
        const financialData = <?php echo json_encode($financial_data); ?>;

        const response = await fetch('http://localhost:5000/generate_tips', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                savings_data: financialData,
                question: question
            })
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
    } catch (error) {
        console.error('Error:', error);
        aiResponse.innerHTML = `
            <div class="bg-red-100 border-l-4 border-red-500 p-4">
                <p class="text-red-700">Error: ${error.message}</p>
            </div>
        `;
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('get-result').addEventListener('click', getAIResponse);
});
</script>
</body>
</html>
