<?php
function fetchUserFinancialData($conn, $user_id) {
    $financial_data = [
        'individual_savings' => 0,
        'monthly_savings' => 0,
        'group_contributions' => [],
        'investments' => [],
        'loans' => [],
        'is_admin' => false,
        'emergency_fund' => 0,
        'emergency_fund_goal' => 0,
    ];

    try {
        // 1. Total Individual Savings
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total_savings FROM savings WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $financial_data['individual_savings'] = $row['total_savings'] ?? 0;

        // 2. Monthly Savings
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS monthly_savings FROM transaction_info WHERE user_id = ? AND MONTH(payment_time) = MONTH(CURRENT_DATE) AND YEAR(payment_time) = YEAR(CURRENT_DATE)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $financial_data['monthly_savings'] = $row['monthly_savings'] ?? 0;

        // 3. Group Contributions
        $stmt = $conn->prepare(
            "SELECT g.group_id, g.group_name, g.goal_amount, g.emergency_fund, 
                    COALESCE(SUM(s.amount), 0) AS user_contribution, 
                    (SELECT COALESCE(SUM(amount), 0) FROM savings WHERE group_id = g.group_id) AS total_group_savings
             FROM group_membership gm
             JOIN my_group g ON gm.group_id = g.group_id
             LEFT JOIN savings s ON g.group_id = s.group_id AND s.user_id = ?
             WHERE gm.user_id = ? AND gm.status = 'approved'
             GROUP BY g.group_id, g.group_name, g.goal_amount, g.emergency_fund"
        );
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $financial_data['group_contributions'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // 4. Investments
        $stmt = $conn->prepare(
            "SELECT i.investment_type AS type, i.amount, i.ex_profit, i.ex_return_date, i.status, 
                    COALESCE(SUM(ir.amount), 0) AS returns,
                    ROUND((COALESCE(SUM(ir.amount), 0) / i.amount) * 100, 2) AS roi
             FROM investments i
             LEFT JOIN investment_returns ir ON i.investment_id = ir.investment_id
             WHERE i.group_id IN (
                 SELECT group_id FROM group_membership WHERE user_id = ? AND status = 'approved'
             )
             GROUP BY i.investment_id, i.investment_type, i.amount, i.ex_profit, i.ex_return_date, i.status"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $financial_data['investments'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // 5. Loans
        $stmt = $conn->prepare(
            "SELECT l.group_id, g.group_name, l.amount, l.return_time, l.status, 
                    COALESCE(SUM(r.amount), 0) AS repaid_amount, 
                    (l.amount - COALESCE(SUM(r.amount), 0)) AS outstanding_amount
             FROM loan_request l
             LEFT JOIN loan_repayments r ON l.id = r.loan_id AND r.status = 'completed'
             JOIN my_group g ON l.group_id = g.group_id
             WHERE l.user_id = ?
             GROUP BY l.id, l.group_id, g.group_name, l.amount, l.return_time, l.status"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $financial_data['loans'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // 6. Check if User is Admin of Any Group
        $stmt = $conn->prepare("SELECT COUNT(*) AS admin_count FROM group_membership WHERE user_id = ? AND is_admin = 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $financial_data['is_admin'] = ($row['admin_count'] > 0);

        // 7. Emergency Fund Overview
        if (!empty($financial_data['group_contributions'])) {
            $financial_data['emergency_fund'] = array_sum(array_column($financial_data['group_contributions'], 'emergency_fund'));
            $financial_data['emergency_fund_goal'] = array_sum(array_column($financial_data['group_contributions'], 'goal_amount'));
        }

    } catch (Exception $e) {
        error_log("Error fetching financial data: " . $e->getMessage());
    }

    return $financial_data;
}
?>
