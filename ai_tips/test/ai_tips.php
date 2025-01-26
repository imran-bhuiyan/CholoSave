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
async function getAIResponse() {
    const savingsData = {
        individual_savings: parseFloat(document.getElementById('individual_savings').value),
        monthly_income: parseFloat(document.getElementById('monthly_income').value),
        monthly_expenses: parseFloat(document.getElementById('monthly_expenses').value),
        group_contributions: parseFloat(document.getElementById('group_contributions').value),
        investments: parseFloat(document.getElementById('investments').value),
        loan_status: document.getElementById('loan_status').value,
        emergency_fund: parseFloat(document.getElementById('emergency_fund').value),
        emergency_fund_goal: parseFloat(document.getElementById('emergency_fund_goal').value)
    };

    const question = document.getElementById('question').value;
    const groupData = {}; // Add group data if available
    const allGroupsData = []; // Add all groups data if available

    const requestData = {
        savings_data: savingsData,
        question: question,
        group_data: groupData,
        all_groups_data: allGroupsData
    };

    console.log('Request Data:', requestData); // Log the request data

    try {
        const response = await fetch('http://127.0.0.1:5000/generate_tips', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        });

        const result = await response.json();

        if (response.ok) {
            aiResponse.innerHTML = `
                <div class="bg-green-100 border-l-4 border-green-500 p-4">
                    <p class="text-green-700">Advice: ${result.advice}</p>
                </div>
            `;
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
