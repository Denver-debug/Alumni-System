-- =====================================================
-- Migration 006: Add Verification Email Templates
-- Adds account_approved and account_rejected email templates
-- for the alumni registration verification workflow
-- =====================================================

-- Insert account_approved email template
INSERT INTO email_templates (
    template_key,
    template_name,
    subject,
    body,
    available_variables,
    description,
    is_active
) VALUES (
    'account_approved',
    'Account Approved',
    'Your Alumni Account Has Been Approved! 🎉',
    '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #1e40af;">Welcome to the Alumni Network!</h2>
        <p>Dear {{name}},</p>
        <p>Great news! Your alumni account has been approved by our administrators.</p>
        <p>You now have full access to:</p>
        <ul style="line-height: 1.8;">
            <li>Alumni directory and networking</li>
            <li>Event registrations and attendance</li>
            <li>Points and rewards system</li>
            <li>Your official Alumni ID card</li>
        </ul>
        <p style="margin-top: 30px;">
            <a href="{{login_url}}" style="background-color: #1e40af; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">Login to Your Account</a>
        </p>
        <p style="margin-top: 20px;">We\'re excited to have you as part of our alumni community!</p>
        <p style="margin-top: 30px; color: #64748b; font-size: 14px;">
            Best regards,<br>
            Alumni Management Team
        </p>
    </div>',
    '["name", "login_url"]',
    'Email sent to alumni when their account is approved by an administrator',
    TRUE
)
ON DUPLICATE KEY UPDATE
    template_name = VALUES(template_name),
    subject = VALUES(subject),
    body = VALUES(body),
    available_variables = VALUES(available_variables),
    description = VALUES(description),
    is_active = VALUES(is_active),
    updated_at = CURRENT_TIMESTAMP;

-- Insert account_rejected email template
INSERT INTO email_templates (
    template_key,
    template_name,
    subject,
    body,
    available_variables,
    description,
    is_active
) VALUES (
    'account_rejected',
    'Account Rejected',
    'Alumni Account Registration Update',
    '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #64748b;">Account Registration Status</h2>
        <p>Dear {{name}},</p>
        <p>Thank you for your interest in joining our alumni network.</p>
        <p>After reviewing your registration, we were unable to verify your account at this time.</p>
        <p style="margin-top: 20px;">
            <strong>Reason:</strong> {{reason}}
        </p>
        <p style="margin-top: 20px;">
            If you believe this is an error or would like to provide additional information, 
            please contact our support team at 
            <a href="mailto:{{support_email}}" style="color: #1e40af;">{{support_email}}</a>.
        </p>
        <p style="margin-top: 20px;">We appreciate your understanding.</p>
        <p style="margin-top: 30px; color: #64748b; font-size: 14px;">
            Best regards,<br>
            Alumni Management Team
        </p>
    </div>',
    '["name", "reason", "support_email"]',
    'Email sent to alumni when their account is rejected by an administrator',
    TRUE
)
ON DUPLICATE KEY UPDATE
    template_name = VALUES(template_name),
    subject = VALUES(subject),
    body = VALUES(body),
    available_variables = VALUES(available_variables),
    description = VALUES(description),
    is_active = VALUES(is_active),
    updated_at = CURRENT_TIMESTAMP;

-- Verify templates were inserted
SELECT 
    template_key,
    template_name,
    subject,
    is_active,
    created_at
FROM email_templates
WHERE template_key IN ('account_approved', 'account_rejected');

SELECT 'Migration 006: Verification Email Templates - COMPLETE' AS status;
