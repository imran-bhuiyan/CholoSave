document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('groupForm');
    const steps = document.querySelectorAll('.form-step');
    const progressFill = document.getElementById('progress-fill');
    const progressSteps = document.querySelectorAll('.progress-step-horizontal');
    const nextButtons = document.querySelectorAll('.btn-next');
    const prevButtons = document.querySelectorAll('.btn-prev');

    let currentStep = 1;
    const totalSteps = steps.length;

    // Update progress bar
    function updateProgress() {
        const percent = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressFill.style.width = percent + '%';
        progressSteps.forEach((step, index) => {
            if (index < currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }

    // Show current step
    function showStep(step) {
        steps.forEach((s) => s.classList.remove('active'));
        document.querySelector(`[data-step="${step}"]`).classList.add('active');
        currentStep = step;
        updateProgress();
    }

    // Next button click
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            const nextStep = parseInt(button.dataset.next);
            if (validateStep(currentStep)) {
                showStep(nextStep);
                updateReview();
            }
        });
    });

    // Previous button click
    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            const prevStep = parseInt(button.dataset.prev);
            showStep(prevStep);
        });
    });

    // Validate current step
    function validateStep(step) {
        const currentStepElement = document.querySelector(`[data-step="${step}"]`);
        const inputs = currentStepElement.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value) {
                isValid = false;
                input.classList.add('error');
            } else {
                input.classList.remove('error');
            }
        });

        return isValid;
    }

    // Update review section
    function updateReview() {
        const reviewFields = {
            'group-name': document.getElementById('group_name').value,
            'description': document.getElementById('description').value,
            'members': document.getElementById('members').value,
            'dps-type': document.getElementById('dps_type').value,
            'time-period': document.getElementById('time_period').value,
            'amount': document.getElementById('amount').value,
            'start-date': document.getElementById('start_date').value,
            'bkash': document.getElementById('bKash').value,
            'rocket': document.getElementById('Rocket').value,
            'nagad': document.getElementById('Nagad').value,
            'goal-amount': document.getElementById('goal_amount').value,
            'emergency-fund': document.getElementById('emergency_fund').value
        };

        Object.entries(reviewFields).forEach(([key, value]) => {
            const element = document.getElementById(`review-${key}`);
            if (element) {
                element.textContent = value || 'Not provided';
            }
        });
    }

    // Initialize
    showStep(1);
});
