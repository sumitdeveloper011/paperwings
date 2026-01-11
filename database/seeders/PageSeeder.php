<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates default pages (About, Privacy, Terms, etc.).
     * Safe to run in production as it checks for existing pages.
     */
    public function run(): void
    {
        // Prevent running in production (pages should be managed manually in production)
        if (app()->environment('production')) {
            $this->command->warn('⚠️  PageSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('🌱 Seeding pages...');

        $pages = [
            [
                'title' => 'About Us',
                'sub_title' => 'Welcome to Paper Wings',
                'content' => '<div class="page-content">
    <h2>About Paper Wings</h2>
    <p>Welcome to Paper Wings, your premier destination for exquisite gifts and thoughtful presents in New Zealand. We are passionate about helping you find the perfect gift for every occasion, whether it\'s a birthday, anniversary, wedding, or just because.</p>

    <h3>Our Story</h3>
    <p>Founded with a vision to make gift-giving effortless and meaningful, Paper Wings has been serving customers across New Zealand since our inception. We understand that finding the right gift can be challenging, which is why we curate a diverse collection of high-quality products that cater to all tastes and preferences.</p>

    <h3>Our Mission</h3>
    <p>At Paper Wings, our mission is simple: to help you express your feelings through carefully selected gifts. We believe that every gift tells a story, and we\'re here to help you tell yours. Whether you\'re looking for something elegant, quirky, practical, or sentimental, we have something special waiting for you.</p>

    <h3>What We Offer</h3>
    <ul>
        <li><strong>Curated Selection:</strong> We handpick each product in our collection to ensure quality and uniqueness</li>
        <li><strong>Nationwide Delivery:</strong> We deliver throughout New Zealand, bringing joy to your doorstep</li>
        <li><strong>Exceptional Service:</strong> Our dedicated team is committed to providing outstanding customer service</li>
        <li><strong>Secure Shopping:</strong> Your privacy and security are our top priorities</li>
    </ul>

    <h3>Why Choose Paper Wings?</h3>
    <p>We pride ourselves on offering a seamless shopping experience with:</p>
    <ul>
        <li>Fast and reliable delivery across New Zealand</li>
        <li>Secure payment options</li>
        <li>Easy returns and exchanges</li>
        <li>Responsive customer support</li>
        <li>Competitive pricing</li>
    </ul>

    <h3>Contact Us</h3>
    <p>Have questions or need assistance? We\'re here to help! Reach out to our friendly customer service team, and we\'ll be happy to assist you with any inquiries.</p>

    <p>Thank you for choosing Paper Wings. We look forward to helping you find the perfect gift!</p>
</div>',
            ],
            [
                'title' => 'Return Policy',
                'sub_title' => 'Hassle-Free Returns & Exchanges',
                'content' => '<div class="page-content">
    <h2>Return Policy</h2>
    <p>At Paper Wings, we want you to be completely satisfied with your purchase. If you\'re not happy with your order, we\'re here to help make it right.</p>

    <h3>Returns & Exchanges</h3>
    <p>You have <strong>14 days</strong> from the date of delivery to return or exchange items purchased from Paper Wings. All returns must be in their original condition, unused, and with all original packaging and tags attached.</p>

    <h3>Eligible Items</h3>
    <p>To be eligible for a return or exchange, your item must:</p>
    <ul>
        <li>Be unused and in the same condition as when you received it</li>
        <li>Be in the original packaging with all tags attached</li>
        <li>Include proof of purchase (order number or receipt)</li>
        <li>Not be a personalized or custom-made item (unless defective)</li>
    </ul>

    <h3>Non-Returnable Items</h3>
    <p>The following items cannot be returned or exchanged:</p>
    <ul>
        <li>Personalized or custom-made items (unless defective)</li>
        <li>Items that have been used, damaged, or altered</li>
        <li>Items without original packaging or tags</li>
        <li>Gift cards</li>
        <li>Items purchased during special sales or promotions (unless defective)</li>
    </ul>

    <h3>How to Return an Item</h3>
    <ol>
        <li><strong>Contact Us:</strong> Email us at our customer service email or call us to initiate a return. Please provide your order number and reason for return.</li>
        <li><strong>Get Authorization:</strong> We\'ll provide you with a Return Authorization (RA) number and return instructions.</li>
        <li><strong>Package Your Item:</strong> Securely package the item in its original packaging with all tags attached.</li>
        <li><strong>Ship It Back:</strong> Send the item to the address provided. We recommend using a tracked shipping service.</li>
    </ol>

    <h3>Return Shipping</h3>
    <p>Return shipping costs are the responsibility of the customer unless the item is defective or we made an error with your order. We recommend using a tracked shipping service to ensure your return reaches us safely.</p>

    <h3>Refunds</h3>
    <p>Once we receive and inspect your returned item, we will process your refund within <strong>5-7 business days</strong>. Refunds will be issued to the original payment method used for the purchase. Please note that it may take additional time for your bank or credit card company to process the refund.</p>

    <h3>Exchanges</h3>
    <p>If you need to exchange an item for a different size, color, or style, please follow the return process above. Once we receive your return, we\'ll process your exchange and ship the new item to you. If the exchange item is of higher value, you\'ll need to pay the difference.</p>

    <h3>Defective or Damaged Items</h3>
    <p>If you receive a defective or damaged item, please contact us immediately. We\'ll arrange for a replacement or full refund, and we\'ll cover the return shipping costs.</p>

    <h3>Questions?</h3>
    <p>If you have any questions about our return policy, please don\'t hesitate to contact our customer service team. We\'re here to help!</p>
</div>',
            ],
            [
                'title' => 'Privacy Policy',
                'sub_title' => 'Your Privacy Matters to Us',
                'content' => '<div class="page-content">
    <h2>Privacy Policy</h2>
    <p>At Paper Wings, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and make purchases.</p>

    <p><strong>Last Updated:</strong> ' . date('F Y') . '</p>

    <h3>Information We Collect</h3>
    <p>We collect information that you provide directly to us, including:</p>
    <ul>
        <li><strong>Personal Information:</strong> Name, email address, phone number, billing and shipping addresses</li>
        <li><strong>Payment Information:</strong> Credit card details, billing information (processed securely through our payment gateway)</li>
        <li><strong>Account Information:</strong> Username, password, and preferences</li>
        <li><strong>Order Information:</strong> Purchase history, order details, and preferences</li>
    </ul>

    <h3>How We Use Your Information</h3>
    <p>We use the information we collect to:</p>
    <ul>
        <li>Process and fulfill your orders</li>
        <li>Communicate with you about your orders, account, and our services</li>
        <li>Send you marketing communications (with your consent)</li>
        <li>Improve our website and customer experience</li>
        <li>Detect and prevent fraud and abuse</li>
        <li>Comply with legal obligations</li>
    </ul>

    <h3>Information Sharing</h3>
    <p>We do not sell your personal information. We may share your information with:</p>
    <ul>
        <li><strong>Service Providers:</strong> Third-party companies that help us operate our business (payment processors, shipping companies, email service providers)</li>
        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety</li>
        <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
    </ul>

    <h3>Data Security</h3>
    <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. This includes:</p>
    <ul>
        <li>SSL encryption for data transmission</li>
        <li>Secure payment processing</li>
        <li>Regular security assessments</li>
        <li>Access controls and authentication</li>
    </ul>

    <h3>Cookies and Tracking Technologies</h3>
    <p>We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, and understand user preferences. You can control cookie settings through your browser preferences.</p>

    <h3>Your Rights (New Zealand Privacy Act 2020)</h3>
    <p>Under the New Zealand Privacy Act 2020, you have the right to:</p>
    <ul>
        <li>Access your personal information</li>
        <li>Request correction of inaccurate information</li>
        <li>Request deletion of your personal information</li>
        <li>Object to processing of your personal information</li>
        <li>Withdraw consent for marketing communications</li>
    </ul>

    <h3>Data Retention</h3>
    <p>We retain your personal information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required by law.</p>

    <h3>Children\'s Privacy</h3>
    <p>Our website is not intended for children under the age of 18. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately.</p>

    <h3>International Data Transfers</h3>
    <p>Your information may be transferred to and processed in countries other than New Zealand. We ensure appropriate safeguards are in place to protect your information in accordance with this Privacy Policy.</p>

    <h3>Changes to This Privacy Policy</h3>
    <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>

    <h3>Contact Us</h3>
    <p>If you have any questions about this Privacy Policy or wish to exercise your rights, please contact us:</p>
    <ul>
        <li>Email: [Your Privacy Email]</li>
        <li>Phone: [Your Privacy Phone]</li>
        <li>Address: [Your Business Address in New Zealand]</li>
    </ul>

    <p>We are committed to resolving any privacy concerns you may have.</p>
</div>',
            ],
            [
                'title' => 'Delivery Policy',
                'sub_title' => 'Fast & Reliable Delivery Across New Zealand',
                'content' => '<div class="page-content">
    <h2>Delivery Policy</h2>
    <p>At Paper Wings, we understand that timely delivery is important to you. We offer fast and reliable delivery services throughout New Zealand to ensure your gifts arrive when you need them.</p>

    <h3>Delivery Areas</h3>
    <p>We deliver to all addresses within New Zealand, including:</p>
    <ul>
        <li>All major cities (Auckland, Wellington, Christchurch, Hamilton, Tauranga, etc.)</li>
        <li>Regional areas</li>
        <li>Rural addresses</li>
        <li>PO Boxes</li>
    </ul>

    <h3>Delivery Options</h3>
    <p>We offer the following delivery options:</p>

    <h4>Standard Delivery</h4>
    <ul>
        <li><strong>Timeframe:</strong> 3-5 business days</li>
        <li><strong>Cost:</strong> Calculated at checkout based on weight and destination</li>
        <li><strong>Tracking:</strong> Included for all standard deliveries</li>
    </ul>

    <h4>Express Delivery</h4>
    <ul>
        <li><strong>Timeframe:</strong> 1-2 business days</li>
        <li><strong>Cost:</strong> Additional fee applies (shown at checkout)</li>
        <li><strong>Tracking:</strong> Included with real-time updates</li>
        <li><strong>Available:</strong> For orders placed before 2:00 PM on business days</li>
    </ul>

    <h4>Same-Day Delivery</h4>
    <ul>
        <li><strong>Timeframe:</strong> Same day (for selected areas)</li>
        <li><strong>Cost:</strong> Additional fee applies</li>
        <li><strong>Available:</strong> For orders placed before 12:00 PM on business days in major metropolitan areas</li>
        <li><strong>Note:</strong> Subject to availability and location</li>
    </ul>

    <h3>Processing Time</h3>
    <p>Orders are typically processed within <strong>1-2 business days</strong> after payment confirmation. During peak seasons (holidays, special promotions), processing may take up to 3 business days.</p>

    <h3>Delivery Timeframes</h3>
    <p>Delivery timeframes are calculated from the date your order is dispatched, not from the date of purchase. Business days exclude weekends and public holidays in New Zealand.</p>

    <h3>Order Tracking</h3>
    <p>Once your order is dispatched, you will receive:</p>
    <ul>
        <li>An email confirmation with your tracking number</li>
        <li>Real-time tracking updates via email or SMS</li>
        <li>Access to track your order through our website</li>
    </ul>

    <h3>Delivery Charges</h3>
    <p>Delivery charges are calculated based on:</p>
    <ul>
        <li>Weight and dimensions of your order</li>
        <li>Delivery destination</li>
        <li>Selected delivery option</li>
    </ul>
    <p>Free delivery may be available for orders over a certain amount. Check our website for current promotions.</p>

    <h3>Delivery Instructions</h3>
    <p>You can provide special delivery instructions during checkout, such as:</p>
    <ul>
        <li>Leave at front door</li>
        <li>Leave with neighbor</li>
        <li>Safe place preferences</li>
        <li>Contact preferences</li>
    </ul>
    <p>We\'ll do our best to follow your instructions, but cannot guarantee specific delivery arrangements.</p>

    <h3>Failed Deliveries</h3>
    <p>If we are unable to deliver your order:</p>
    <ul>
        <li>We will attempt delivery up to 3 times</li>
        <li>You will be notified via email or phone</li>
        <li>The package may be held at a local depot for collection</li>
        <li>If uncollected, the order may be returned to us (additional charges may apply)</li>
    </ul>

    <h3>Public Holidays</h3>
    <p>Deliveries are not made on New Zealand public holidays. Orders placed during public holidays will be processed on the next business day.</p>

    <h3>Rural Deliveries</h3>
    <p>Rural deliveries may take an additional 1-2 business days. Some remote areas may have limited delivery options. Please contact us if you have concerns about delivery to your address.</p>

    <h3>International Delivery</h3>
    <p>Currently, we only deliver within New Zealand. For international delivery inquiries, please contact our customer service team.</p>

    <h3>Gift Delivery</h3>
    <p>If you\'re sending a gift, we can:</p>
    <ul>
        <li>Include a gift message (free)</li>
        <li>Use gift packaging (if available)</li>
        <li>Exclude pricing information from the package</li>
    </ul>
    <p>Please specify these options during checkout.</p>

    <h3>Damaged or Lost Packages</h3>
    <p>If your package arrives damaged or is lost in transit:</p>
    <ul>
        <li>Contact us immediately (within 48 hours of delivery)</li>
        <li>We\'ll investigate and arrange a replacement or refund</li>
        <li>We may request photos of damaged items</li>
    </ul>

    <h3>Questions About Delivery?</h3>
    <p>If you have any questions about delivery, estimated delivery times, or need to make special arrangements, please contact our customer service team. We\'re here to help ensure your order arrives safely and on time!</p>
</div>',
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'sub_title' => 'Terms of Service',
                'content' => '<div class="page-content">
    <h2>Terms and Conditions</h2>
    <p>Welcome to Paper Wings. These Terms and Conditions govern your use of our website and services. By accessing or using our website, you agree to be bound by these terms. Please read them carefully.</p>

    <p><strong>Last Updated:</strong> ' . date('F Y') . '</p>

    <h3>1. Acceptance of Terms</h3>
    <p>By accessing and using the Paper Wings website, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>

    <h3>2. Use License</h3>
    <p>Permission is granted to temporarily download one copy of the materials on Paper Wings\' website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
    <ul>
        <li>Modify or copy the materials</li>
        <li>Use the materials for any commercial purpose or for any public display</li>
        <li>Attempt to decompile or reverse engineer any software contained on the website</li>
        <li>Remove any copyright or other proprietary notations from the materials</li>
        <li>Transfer the materials to another person or "mirror" the materials on any other server</li>
    </ul>

    <h3>3. Account Registration</h3>
    <p>To make purchases on our website, you may be required to create an account. You agree to:</p>
    <ul>
        <li>Provide accurate, current, and complete information during registration</li>
        <li>Maintain and update your information to keep it accurate</li>
        <li>Maintain the security of your password and identification</li>
        <li>Accept all responsibility for activities that occur under your account</li>
        <li>Notify us immediately of any unauthorized use of your account</li>
    </ul>

    <h3>4. Products and Pricing</h3>
    <p>We strive to provide accurate product descriptions, images, and pricing. However, we do not warrant that product descriptions or other content on this site is accurate, complete, reliable, current, or error-free.</p>
    <ul>
        <li>Prices are subject to change without notice</li>
        <li>We reserve the right to modify or discontinue products at any time</li>
        <li>All prices are in New Zealand Dollars (NZD) unless otherwise stated</li>
        <li>We reserve the right to refuse or cancel any order at our discretion</li>
    </ul>

    <h3>5. Payment Terms</h3>
    <p>Payment must be received before we process and ship your order. We accept various payment methods including credit cards and other secure payment gateways. By providing payment information, you represent that you are authorized to use the payment method.</p>

    <h3>6. Shipping and Delivery</h3>
    <p>Shipping terms are outlined in our Delivery Policy. We are not responsible for delays caused by shipping carriers or customs. Delivery times are estimates and not guaranteed.</p>

    <h3>7. Returns and Refunds</h3>
    <p>Our return and refund policy is detailed in our Return Policy. Returns must be made within the specified timeframe and in accordance with our return policy terms.</p>

    <h3>8. Intellectual Property</h3>
    <p>All content on this website, including but not limited to text, graphics, logos, images, audio clips, digital downloads, and software, is the property of Paper Wings or its content suppliers and is protected by New Zealand and international copyright laws.</p>

    <h3>9. Prohibited Uses</h3>
    <p>You may not use our website:</p>
    <ul>
        <li>In any way that violates any applicable law or regulation</li>
        <li>To transmit any malicious code, viruses, or harmful data</li>
        <li>To impersonate or attempt to impersonate the company or any other person</li>
        <li>In any way that infringes upon the rights of others</li>
        <li>To engage in any automated use of the system</li>
    </ul>

    <h3>10. Limitation of Liability</h3>
    <p>To the fullest extent permitted by law, Paper Wings shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses resulting from your use of the website.</p>

    <h3>11. Indemnification</h3>
    <p>You agree to defend, indemnify, and hold harmless Paper Wings and its officers, directors, employees, and agents from and against any claims, liabilities, damages, losses, and expenses, including without limitation, reasonable legal and accounting fees, arising out of or in any way connected with your access to or use of the website or your violation of these Terms.</p>

    <h3>12. Governing Law</h3>
    <p>These Terms shall be governed by and construed in accordance with the laws of New Zealand, without regard to its conflict of law provisions. Any disputes arising under or in connection with these Terms shall be subject to the exclusive jurisdiction of the courts of New Zealand.</p>

    <h3>13. Changes to Terms</h3>
    <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>

    <h3>14. Contact Information</h3>
    <p>If you have any questions about these Terms and Conditions, please contact us:</p>
    <ul>
        <li>Email: [Your Contact Email]</li>
        <li>Phone: [Your Contact Phone]</li>
        <li>Address: [Your Business Address in New Zealand]</li>
    </ul>

    <h3>15. Severability</h3>
    <p>If any provision of these Terms is found to be unenforceable or invalid, that provision shall be limited or eliminated to the minimum extent necessary so that these Terms shall otherwise remain in full force and effect.</p>

    <p>By using our website, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</p>
</div>',
            ],
            [
                'title' => 'Cookie Policy',
                'slug' => 'cookie-policy',
                'sub_title' => 'How We Use Cookies',
                'content' => '<div class="page-content">
    <h2>Cookie Policy</h2>
    <p>This Cookie Policy explains how Paper Wings uses cookies and similar tracking technologies on our website. By using our website, you consent to the use of cookies in accordance with this policy.</p>

    <p><strong>Last Updated:</strong> ' . date('F Y') . '</p>

    <h3>What Are Cookies?</h3>
    <p>Cookies are small text files that are placed on your computer or mobile device when you visit a website. Cookies are widely used to make websites work more efficiently and to provide information to website owners.</p>

    <h3>How We Use Cookies</h3>
    <p>We use cookies for various purposes, including:</p>
    <ul>
        <li><strong>Essential Cookies:</strong> These cookies are necessary for the website to function properly. They enable core functionality such as security, network management, and accessibility.</li>
        <li><strong>Performance Cookies:</strong> These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously.</li>
        <li><strong>Functionality Cookies:</strong> These cookies allow the website to remember choices you make (such as your username, language, or region) and provide enhanced, personalized features.</li>
        <li><strong>Targeting/Advertising Cookies:</strong> These cookies are used to deliver advertisements relevant to you and your interests. They also help measure the effectiveness of advertising campaigns.</li>
    </ul>

    <h3>Types of Cookies We Use</h3>

    <h4>1. Strictly Necessary Cookies</h4>
    <p>These cookies are essential for you to browse the website and use its features. Without these cookies, services you have requested cannot be provided. These cookies do not store any personally identifiable information.</p>
    <ul>
        <li>Session management</li>
        <li>Security</li>
        <li>Load balancing</li>
        <li>Shopping cart functionality</li>
    </ul>

    <h4>2. Performance and Analytics Cookies</h4>
    <p>These cookies collect information about how you use our website, such as which pages you visit most often. This data helps us optimize our website and improve user experience.</p>
    <ul>
        <li>Google Analytics (if used)</li>
        <li>Page view tracking</li>
        <li>User behavior analysis</li>
    </ul>

    <h4>3. Functionality Cookies</h4>
    <p>These cookies allow the website to remember choices you make and provide enhanced, personalized features.</p>
    <ul>
        <li>Language preferences</li>
        <li>Region selection</li>
        <li>User interface customization</li>
        <li>Remembering login information</li>
    </ul>

    <h4>4. Targeting and Advertising Cookies</h4>
    <p>These cookies are used to deliver advertisements that are relevant to you and your interests. They also help measure the effectiveness of advertising campaigns.</p>
    <ul>
        <li>Social media integration</li>
        <li>Advertising network cookies</li>
        <li>Retargeting cookies</li>
    </ul>

    <h3>Third-Party Cookies</h3>
    <p>In addition to our own cookies, we may also use various third-party cookies to report usage statistics of the website, deliver advertisements, and so on. These third parties may include:</p>
    <ul>
        <li><strong>Google Analytics:</strong> To analyze website traffic and user behavior</li>
        <li><strong>Social Media Platforms:</strong> For social media integration and sharing</li>
        <li><strong>Payment Processors:</strong> For secure payment processing</li>
        <li><strong>Advertising Networks:</strong> For delivering relevant advertisements</li>
    </ul>

    <h3>Managing Cookies</h3>
    <p>You have the right to decide whether to accept or reject cookies. You can exercise your cookie rights by setting your preferences in your browser settings.</p>

    <h4>Browser Settings</h4>
    <p>Most web browsers allow you to control cookies through their settings preferences. However, limiting cookies may impact your ability to use our website. Here\'s how to manage cookies in popular browsers:</p>
    <ul>
        <li><strong>Google Chrome:</strong> Settings > Privacy and Security > Cookies and other site data</li>
        <li><strong>Mozilla Firefox:</strong> Options > Privacy & Security > Cookies and Site Data</li>
        <li><strong>Safari:</strong> Preferences > Privacy > Cookies and website data</li>
        <li><strong>Microsoft Edge:</strong> Settings > Privacy, search, and services > Cookies and site permissions</li>
    </ul>

    <h3>Cookie Consent</h3>
    <p>When you first visit our website, you may be presented with a cookie consent banner. You can choose to accept all cookies, reject non-essential cookies, or customize your preferences.</p>

    <h3>Do Not Track Signals</h3>
    <p>Some browsers incorporate a "Do Not Track" (DNT) feature that signals to websites you visit that you do not want to have your online activity tracked. Currently, there is no standard for how DNT signals should be interpreted. Our website does not currently respond to DNT signals.</p>

    <h3>Cookies and Personal Information</h3>
    <p>Most cookies we use do not collect information that identifies you personally. They collect more general information such as how users arrive at and use our website, or a user\'s general location. However, some cookies may collect personal information in accordance with our Privacy Policy.</p>

    <h3>Changes to This Cookie Policy</h3>
    <p>We may update this Cookie Policy from time to time to reflect changes in technology, legislation, or our data use practices. We will notify you of any material changes by posting the new Cookie Policy on this page and updating the "Last Updated" date.</p>

    <h3>More Information About Cookies</h3>
    <p>If you would like more information about cookies, you can visit:</p>
    <ul>
        <li><a href="https://www.allaboutcookies.org" target="_blank" rel="noopener">www.allaboutcookies.org</a></li>
        <li><a href="https://www.youronlinechoices.com" target="_blank" rel="noopener">www.youronlinechoices.com</a></li>
    </ul>

    <h3>Contact Us</h3>
    <p>If you have any questions about our use of cookies or this Cookie Policy, please contact us:</p>
    <ul>
        <li>Email: [Your Contact Email]</li>
        <li>Phone: [Your Contact Phone]</li>
        <li>Address: [Your Business Address in New Zealand]</li>
    </ul>

    <p>We are committed to being transparent about our use of cookies and helping you understand how they affect your browsing experience.</p>
</div>',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($pages as $pageData) {
            // Check if page already exists by slug (use provided slug or generate from title)
            $slug = $pageData['slug'] ?? Str::slug($pageData['title']);
            $existingPage = Page::where('slug', $slug)->first();

            if (!$existingPage) {
                Page::create($pageData);
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->command->info("✅ Pages seeded successfully!");
        $this->command->info("  • Created: {$created}");
        $this->command->info("  • Skipped (already exist): {$skipped}");
        $this->command->info("  • Total: " . count($pages));
    }
}

