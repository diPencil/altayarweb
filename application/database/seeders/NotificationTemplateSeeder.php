<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            [
                'act' => 'legacy_user_password_setup',
                'name' => 'Legacy User - Password Setup',
                'subj' => 'Welcome to AltayarVIP - Set Your New Password',
                'email_body' => "Hello {{first_name}},\n\nYour AltayarVIP account has been moved to our new system.\n\nFor your security, please set a new password using the secure link below:\n\n{{password_setup_link}}\n\nThis link is unique to your account and will expire for security reasons.\n\nIf you did not request this, you can ignore this email.\n\nThank you,\nAltayarVIP Team\n\n---\n\nمرحبًا {{first_name}},\n\nتم نقل حسابك في AltayarVIP إلى النظام الجديد.\n\nلحماية حسابك، برجاء تعيين كلمة مرور جديدة من خلال الرابط الآمن التالي:\n\n{{password_setup_link}}\n\nهذا الرابط خاص بحسابك فقط وتنتهي صلاحيته خلال مدة محددة لأسباب أمنية.\n\nإذا لم تطلب ذلك، يمكنك تجاهل هذه الرسالة.\n\nشكرًا لك،\nفريق AltayarVIP",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'email' => "User's email",
                    'password_setup_link' => "Link to setup password",
                    'expire_time' => "Link expiration time",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'account_pending_activation',
                'name' => 'Account - Pending Activation',
                'subj' => 'Your AltayarVIP Account is Pending Activation',
                'email_body' => "Hello {{first_name}},\n\nYour account has been created successfully and is currently pending activation.\n\nPlease contact the admin team to activate your account according to the required membership.\n\nThank you,\nAltayarVIP Team\n\n---\n\nمرحبًا {{first_name}},\n\nتم إنشاء حسابك بنجاح، وهو حاليًا قيد التفعيل.\n\nبرجاء التواصل مع الإدارة لتفعيل حسابك حسب العضوية المطلوبة.\n\nشكرًا لك،\nفريق AltayarVIP",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'email' => "User's email",
                    'site_name' => "Site name",
                    'contact_link' => "Link to contact support"
                ]
            ],
            [
                'act' => 'membership_activated_by_admin',
                'name' => 'Membership - Activated By Admin',
                'subj' => 'Your AltayarVIP Membership Has Been Activated',
                'email_body' => "Hello {{first_name}},\n\nYour membership has been activated successfully.\n\nMembership: {{membership_plan}}\nMember ID: {{member_id}}\nValid Until: {{valid_until}}\n\nYou can now access your dashboard and use your membership benefits.\n\nThank you,\nAltayarVIP Team\n\n---\n\nمرحبًا {{first_name}},\n\nتم تفعيل عضويتك بنجاح.\n\nالعضوية: {{membership_plan}}\nمعرف العضو: {{member_id}}\nصالحة حتى: {{valid_until}}\n\nيمكنك الآن الدخول إلى لوحة التحكم واستخدام مزايا عضويتك.\n\nشكرًا لك،\nفريق AltayarVIP",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'email' => "User's email",
                    'membership_plan' => "Plan name",
                    'member_id' => "Member ID",
                    'valid_until' => "Expiry date",
                    'dashboard_link' => "Link to dashboard",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'legacy_booking_imported',
                'name' => 'Legacy Booking - Imported',
                'subj' => 'Your Previous AltayarVIP Bookings Are Now Available',
                'email_body' => "Hello {{first_name}},\n\nYour previous bookings have been moved to the new AltayarVIP system and are now available in your dashboard.\n\nYou can review them from your bookings page.\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'booking_count' => "Number of bookings",
                    'bookings_link' => "Link to bookings page",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'legacy_cashback_added',
                'name' => 'Legacy Cashback - Added',
                'subj' => 'Legacy Cashback Added to Your AltayarVIP Account',
                'email_body' => "Hello {{first_name}},\n\nA legacy cashback balance from your previous membership transactions has been added to your account.\n\nAmount: {{cashback_amount}}\n\nYou can review it from your dashboard.\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'cashback_amount' => "Amount of cashback",
                    'cashback_link' => "Link to cashback details",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'booking_created_by_admin',
                'name' => 'Booking - Created By Admin',
                'subj' => 'Your AltayarVIP Booking Has Been Created',
                'email_body' => "Hello {{first_name}},\n\nA new booking has been created for you by the administrator.\n\nBooking: {{booking_title}}\nType: {{booking_type}}\nReference: {{reference_no}}\nService Date: {{service_date}}\nEnd Date: {{end_date}}\nAmount: {{amount}}\n\nYou can view the details here: {{booking_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'booking_title' => "Booking Title",
                    'booking_type' => "Booking Type",
                    'reference_no' => "Reference Number",
                    'service_date' => "Service Date",
                    'end_date' => "End Date",
                    'amount' => "Amount",
                    'booking_link' => "Link to booking",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'booking_status_changed',
                'name' => 'Booking - Status Changed',
                'subj' => 'Your AltayarVIP Booking Status Has Been Updated',
                'email_body' => "Hello {{first_name}},\n\nThe status of your booking has been updated.\n\nBooking: {{booking_title}}\nReference: {{reference_no}}\nOld Status: {{old_status}}\nNew Status: {{new_status}}\n\nYou can view the updated booking here: {{booking_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'booking_title' => "Booking Title",
                    'reference_no' => "Reference Number",
                    'old_status' => "Previous Status",
                    'new_status' => "Current Status",
                    'booking_link' => "Link to booking",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'invoice_created',
                'name' => 'Invoice - Created',
                'subj' => 'New Invoice Created for Your AltayarVIP Account',
                'email_body' => "Hello {{first_name}},\n\nA new invoice has been generated for your account.\n\nInvoice Number: {{invoice_number}}\nAmount: {{invoice_amount}}\nIssue Date: {{issue_date}}\n\nYou can view and pay your invoice here: {{invoice_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'invoice_number' => "Invoice Number",
                    'invoice_amount' => "Invoice Amount",
                    'issue_date' => "Issue Date",
                    'invoice_link' => "Link to invoice",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'invoice_marked_paid',
                'name' => 'Invoice - Marked Paid',
                'subj' => 'Your AltayarVIP Invoice Has Been Marked as Paid',
                'email_body' => "Hello {{first_name}},\n\nYour invoice has been successfully marked as paid.\n\nInvoice Number: {{invoice_number}}\nAmount Paid: {{paid_amount}}\nPayment Date: {{payment_date}}\n\nYou can view the invoice details here: {{invoice_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'invoice_number' => "Invoice Number",
                    'paid_amount' => "Paid Amount",
                    'payment_date' => "Payment Date",
                    'invoice_link' => "Link to invoice",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'account_activated_by_admin',
                'name' => 'Account - Activated By Admin',
                'subj' => 'Your AltayarVIP Account Has Been Activated',
                'email_body' => "Hello {{first_name}},\n\nYour account has been activated by the administrator. You now have full access to our services and your membership benefits.\n\nYou can log in to your dashboard here: {{dashboard_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'email' => "User's email",
                    'dashboard_link' => "Link to dashboard",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'password_setup_reminder',
                'name' => 'Password Setup - Reminder',
                'subj' => 'Reminder: Set Your AltayarVIP Password',
                'email_body' => "Hello {{first_name}},\n\nThis is a friendly reminder to set your account password to secure your account and access your dashboard.\n\nPlease use the following secure link:\n\n{{password_setup_link}}\n\nThis link will expire at {{expire_time}}.\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'password_setup_link' => "Link to setup password",
                    'expire_time' => "Link expiration time",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'support_new_admin_message',
                'name' => 'Support - New Message From Admin',
                'subj' => 'New Message from AltayarVIP Support',
                'email_body' => "Hello {{first_name}},\n\nYou have received a new response from our support team regarding ticket #{{ticket_id}}.\n\nMessage Preview:\n{{message_preview}}\n\nYou can view the full conversation and reply here: {{support_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'ticket_id' => "Ticket ID",
                    'message_preview' => "Message Preview",
                    'support_link' => "Link to support ticket",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'cashback_used_in_booking',
                'name' => 'Cashback - Used In Booking',
                'subj' => 'Cashback Used for Your AltayarVIP Booking',
                'email_body' => "Hello {{first_name}},\n\nCashback funds have been applied to your booking.\n\nBooking: {{booking_title}}\nCashback Amount Used: {{cashback_amount}}\nRemaining Cashback Balance: {{cashback_balance}}\n\nYou can view the booking here: {{booking_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'booking_title' => "Booking Title",
                    'cashback_amount' => "Cashback Amount",
                    'cashback_balance' => "Remaining Balance",
                    'booking_link' => "Link to booking",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'wallet_refund_requested',
                'name' => 'Wallet - Refund Requested',
                'subj' => 'Your Refund Request Has Been Submitted',
                'email_body' => "Hello {{first_name}},\n\nYour request for a wallet refund has been successfully submitted and is currently being reviewed by our team.\n\nRefund Amount: {{refund_amount}}\nRequest Date: {{request_date}}\n\nYou can track the status in your wallet dashboard: {{wallet_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'refund_amount' => "Requested Amount",
                    'request_date' => "Request Date",
                    'wallet_link' => "Link to wallet",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'wallet_refund_approved',
                'name' => 'Wallet - Refund Approved',
                'subj' => 'Your Refund Request Has Been Approved',
                'email_body' => "Hello {{first_name}},\n\nGood news! Your wallet refund request has been approved.\n\nRefund Amount: {{refund_amount}}\nNew Wallet Balance: {{wallet_balance}}\n\nYou can view your wallet here: {{wallet_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'refund_amount' => "Approved Amount",
                    'wallet_balance' => "New Balance",
                    'wallet_link' => "Link to wallet",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'wallet_refund_rejected',
                'name' => 'Wallet - Refund Rejected',
                'subj' => 'Your Refund Request Has Been Rejected',
                'email_body' => "Hello {{first_name}},\n\nYour wallet refund request has been reviewed and rejected.\n\nRefund Amount: {{refund_amount}}\nReason: {{reason}}\n\nYou can view the details here: {{wallet_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'refund_amount' => "Rejected Amount",
                    'reason' => "Reason for Rejection",
                    'wallet_link' => "Link to wallet",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'legacy_booking_data_available',
                'name' => 'Legacy Booking - Data Available',
                'subj' => 'Your Previous AltayarVIP Bookings Are Now Available',
                'email_body' => "Hello {{first_name}},\n\nYour historical booking data from our previous system has been successfully imported and is now available for your review.\n\nImported Records: {{booking_count}}\n\nYou can access your legacy data here: {{bookings_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'booking_count' => "Number of bookings",
                    'bookings_link' => "Link to bookings page",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'account_profile_updated',
                'name' => 'Account - Profile Updated',
                'subj' => 'Your AltayarVIP Profile Has Been Updated',
                'email_body' => "Hello {{first_name}},\n\nYour account profile has been successfully updated.\n\nUpdated Fields: {{updated_fields}}\n\nYou can review your profile here: {{profile_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'updated_fields' => "List of updated fields",
                    'profile_link' => "Link to profile",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'account_email_changed',
                'name' => 'Account - Email Changed',
                'subj' => 'Your AltayarVIP Email Has Been Changed',
                'email_body' => "Hello {{first_name}},\n\nThe email address associated with your AltayarVIP account has been changed.\n\nOld Email: {{old_email}}\nNew Email: {{new_email}}\n\nIf you did not perform this change, please contact our support team immediately: {{support_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'old_email' => "Previous Email",
                    'new_email' => "New Email",
                    'support_link' => "Link to support",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'account_mobile_changed',
                'name' => 'Account - Mobile Changed',
                'subj' => 'Your AltayarVIP Mobile Number Has Been Changed',
                'email_body' => "Hello {{first_name}},\n\nThe mobile number associated with your AltayarVIP account has been changed.\n\nOld Mobile: {{old_mobile}}\nNew Mobile: {{new_mobile}}\n\nIf you did not perform this change, please contact our support team immediately: {{support_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'old_mobile' => "Previous Mobile",
                    'new_mobile' => "New Mobile",
                    'support_link' => "Link to support",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'account_suspended',
                'name' => 'Account - Suspended',
                'subj' => 'Your AltayarVIP Account Has Been Suspended',
                'email_body' => "Hello {{first_name}},\n\nYour AltayarVIP account has been suspended.\n\nReason: {{reason}}\n\nIf you believe this is an error or wish to appeal this decision, please contact support: {{support_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'reason' => "Reason for suspension",
                    'support_link' => "Link to support",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'account_reactivated',
                'name' => 'Account - Reactivated',
                'subj' => 'Your AltayarVIP Account Has Been Reactivated',
                'email_body' => "Hello {{first_name}},\n\nWe are pleased to inform you that your AltayarVIP account has been reactivated. You can now log in and resume using our services.\n\nDashboard: {{dashboard_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'dashboard_link' => "Link to dashboard",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'customer_employee_assigned',
                'name' => 'Customer - Employee Assigned',
                'subj' => 'Your AltayarVIP Account Manager Has Been Assigned',
                'email_body' => "Hello {{first_name}},\n\nA dedicated account manager has been assigned to your AltayarVIP account to assist you with your bookings and membership.\n\nManager Name: {{employee_name}}\nContact Information: {{employee_contact}}\n\nYou can also find this information in your dashboard: {{dashboard_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'employee_name' => "Employee Name",
                    'employee_contact' => "Employee Contact Info",
                    'dashboard_link' => "Link to dashboard",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'booking_service_reminder',
                'name' => 'Booking - Reminder Before Service Date',
                'subj' => 'Reminder: Your AltayarVIP Booking Is Coming Soon',
                'email_body' => "Hello {{first_name}},\n\nThis is a reminder regarding your upcoming booking with AltayarVIP.\n\nBooking: {{booking_title}}\nReference: {{reference_no}}\nService Date: {{service_date}}\n\nWe look forward to serving you. You can review your booking details here: {{booking_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'booking_title' => "Booking Title",
                    'reference_no' => "Reference Number",
                    'service_date' => "Service Date",
                    'booking_link' => "Link to booking",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'booking_cancelled_by_admin',
                'name' => 'Booking - Cancelled By Admin',
                'subj' => 'Your AltayarVIP Booking Has Been Cancelled',
                'email_body' => "Hello {{first_name}},\n\nYour booking has been cancelled by the administrator.\n\nBooking: {{booking_title}}\nReference: {{reference_no}}\nReason: {{reason}}\n\nYou can view the cancellation details here: {{booking_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'booking_title' => "Booking Title",
                    'reference_no' => "Reference Number",
                    'reason' => "Reason for cancellation",
                    'booking_link' => "Link to booking",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'payment_failed',
                'name' => 'Payment - Failed',
                'subj' => 'Your AltayarVIP Payment Was Not Completed',
                'email_body' => "Hello {{first_name}},\n\nWe were unable to process your payment for the following invoice.\n\nInvoice Number: {{invoice_number}}\nAmount: {{payment_amount}}\n\nPlease try again using the following link: {{payment_link}}\n\nIf you continue to experience issues, please contact support: {{support_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'payment_amount' => "Payment Amount",
                    'invoice_number' => "Invoice Number",
                    'payment_link' => "Link to payment page",
                    'support_link' => "Link to support",
                    'site_name' => "Site name"
                ]
            ],
            [
                'act' => 'payment_pending_review',
                'name' => 'Payment - Pending Review',
                'subj' => 'Your AltayarVIP Payment Is Pending Review',
                'email_body' => "Hello {{first_name}},\n\nYour payment has been received and is currently pending review by our financial team.\n\nInvoice Number: {{invoice_number}}\nAmount: {{payment_amount}}\n\nYou will receive a confirmation once the payment is approved. If you have any questions, please contact support: {{support_link}}\n\nThank you,\nAltayarVIP Team",
                'shortcodes' => [
                    'first_name' => "User's first name",
                    'username' => "User's username",
                    'payment_amount' => "Payment Amount",
                    'invoice_number' => "Invoice Number",
                    'support_link' => "Link to support",
                    'site_name' => "Site name"
                ]
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['act' => $template['act']],
                [
                    'name' => $template['name'],
                    'subj' => $template['subj'],
                    'email_body' => $template['email_body'],
                    'shortcodes' => $template['shortcodes'],
                    'email_status' => 1,
                    'sms_status' => 0,
                ]
            );
        }
    }
}
