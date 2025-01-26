from flask import Flask, request, jsonify
from flask_cors import CORS
from groq import Groq
import os

app = Flask(__name__)
CORS(app)

# Initialize Groq client
client = Groq(
    api_key=os.environ.get("GROQ_API_KEY"),
)

def generate_financial_advice(savings_data, question, group_data=None, all_groups_data=None):
    try:
        # Prepare group-specific or all-groups details
        if all_groups_data:
            group_details = "\n".join([
                f"  - {group['group_name']}: BDT {float(group['total_contribution']):,.2f} saved out of BDT {float(group['goal_amount']):,.2f}, Emergency Fund: BDT {float(group['emergency_fund']):,.2f}"
                for group in all_groups_data
            ])
            group_summary = f"""
            Group Contributions Overview:
            {group_details}
            """
        elif group_data:
            group_summary = f"""
            Selected Group:
            Group Name: {group_data['group_name']}
            - Total contribution: BDT {float(group_data['total_contribution']):,.2f}
            - Goal amount: BDT {float(group_data['goal_amount']):,.2f}
            - Emergency fund: BDT {float(group_data['emergency_fund']):,.2f}
            """
        else:
            group_summary = "No specific group or group data provided."

        # Build the prompt
        prompt = f"""
        You are an AI financial advisor specializing in actionable advice tailored to users in Bangladesh. Provide detailed recommendations based on the user's financial data and selected group(s). Consider the local economic situation, investment options, inflation, and regulations.

        User Financial Profile:
        - Total savings: BDT {float(savings_data['individual_savings']):,.2f}
        - Monthly income: BDT {float(savings_data['monthly_income']):,.2f}
        - Monthly expenses: BDT {float(savings_data['monthly_expenses']):,.2f}
        - Loan status: {savings_data['loan_status']}
        - Emergency fund: BDT {float(savings_data['emergency_fund']):,.2f} out of BDT {float(savings_data['emergency_fund_goal']):,.2f}

        {group_summary}

        User’s Question:
        "{question}"

        Instructions:
        1. Answer the user's question with actionable advice based on their financial profile and selected group(s). Show advice in 3-4 lines. 
        2. If \"All Groups\" is selected, provide a high-level strategy to improve group savings, contributions, and emergency fund goals collectively.
        Respond in a friendly, professional tone. Use bullet points or numbered lists for clarity. Use, Hello as greeting.
        """
        #If \"All Groups\" is selected, provide a high-level strategy to improve group savings, contributions, and emergency fund goals collectively.
        #3. Suggest additional savings, investment, or budgeting strategies relevant to the Bangladeshi market (e.g., NSCs, FDs, stock market).
        #4. Highlight potential risks, including inflation, market volatility, or regulatory changes, and suggest mitigation strategies.
        #1. Summarize the user’s financial situation in 1-2 sentences.
        # Generate response using Groq
        completion = client.chat.completions.create(
            model="llama-3.3-70b-versatile",
            messages=[
                {
                    "role": "system",
                    "content": "You are a professional financial advisor providing actionable advice tailored to users in Bangladesh."
                },
                {
                    "role": "user",
                    "content": prompt
                }
            ],
            temperature=0.7,
            max_tokens=1024
        )

        response = completion.choices[0].message.content

        # Format headers: replace **header** with <strong>header</strong>
        formatted_response = response.replace("**", "<strong>").replace("**", "</strong>")

        # Parse the response into structured format
        advice = {
        'title': 'Financial Advice (Bangladesh)',
        'main_advice': formatted_response.split('\n')[0],
        'steps': [step.strip() for step in formatted_response.split('\n')[1:] if step.strip()]
        }

        return advice

    except Exception as e:
        print(f"Error generating advice: {e}")
        return None

@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        data = request.get_json()
        savings_data = data.get('savings_data', {})
        question = data.get('question')
        group_data = data.get('group_data')
        all_groups_data = data.get('all_groups_data')

        # Validate inputs
        if not question:
            return jsonify({
                'status': 'error',
                'error': 'No question provided. Please select or enter a question.'
            }), 400

        required_fields = ['individual_savings', 'monthly_income', 'monthly_expenses', 'group_contributions', 'investments', 'loan_status', 'emergency_fund', 'emergency_fund_goal']
        for field in required_fields:
            if field not in savings_data:
                return jsonify({
                    'status': 'error',
                    'error': f"Missing field in savings_data: {field}"
                }), 400

        # Generate financial advice
        advice = generate_financial_advice(savings_data, question, group_data, all_groups_data)

        if advice:
            return jsonify({
                'status': 'success',
                'advice': advice
            })
        else:
            return jsonify({
                'status': 'error',
                'error': 'Failed to generate advice'
            }), 500

    except Exception as e:
        return jsonify({
            'status': 'error',
            'error': str(e)
        }), 500

if __name__ == '__main__':
    app.run(debug=True)
