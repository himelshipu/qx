<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Privacy Policy
        StaticPage::firstOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'content' => <<<'HTML'
<h2>Privacy Policy</h2>
<p>This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our platform.</p>

<h3>Information We Collect</h3>
<p>We may collect information about you in a variety of ways. The information we may collect on the site includes:</p>
<ul>
<li><strong>Personal Data:</strong> Email address, name, phone number, and other contact information you provide.</li>
<li><strong>Browsing Data:</strong> Information about your interactions with our platform, including pages visited and actions taken.</li>
<li><strong>Device Information:</strong> Device type, operating system, and browser information.</li>
</ul>

<h3>How We Use Your Information</h3>
<p>We use the information we collect to:</p>
<ul>
<li>Provide, maintain, and improve our services</li>
<li>Send promotional communications (with your consent)</li>
<li>Comply with legal obligations</li>
<li>Prevent fraudulent activity</li>
</ul>

<h3>Data Security</h3>
<p>We implement appropriate technical and organizational security measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction.</p>

<h3>Contact Us</h3>
<p>If you have questions about this Privacy Policy, please contact us at support@example.com</p>
HTML,
                'meta_description' => 'Learn about how we protect and use your personal information.',
                'meta_keywords' => 'privacy, data protection, terms, policy',
                'is_active' => true,
            ]
        );

        // Terms of Use
        StaticPage::firstOrCreate(
            ['slug' => 'terms-of-use'],
            [
                'title' => 'Terms of Use',
                'content' => <<<'HTML'
<h2>Terms of Use</h2>
<p>Please read these Terms of Use carefully before using our platform. By accessing and using this site, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h3>License to Use</h3>
<p>Unless otherwise stated, we own the intellectual property rights for all material on this site. All intellectual property rights are reserved. You may view and print pages from the site for personal use, subject to restrictions set in these terms and conditions.</p>

<h3>Prohibited Behavior</h3>
<p>You must not:</p>
<ul>
<li>Republish material from this site without proper attribution</li>
<li>Sell, rent or sub-license material from this site</li>
<li>Reproduce, duplicate or copy material for commercial purposes</li>
<li>Attempt to gain unauthorized access to restricted portions of the site</li>
</ul>

<h3>Limitation of Liability</h3>
<p>The information on this site is provided on an "as is" basis. To the fullest extent permitted by law, this site disclaims all representations and warranties, express or implied, concerning the site and its content.</p>

<h3>Modifications to Terms</h3>
<p>We may revise these terms of use for this site at any time without notice. By using this site, you are agreeing to be bound by the then current version of these terms of use.</p>

<h3>Governing Law</h3>
<p>These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which the company is located.</p>
HTML,
                'meta_description' => 'Review the terms and conditions for using our platform.',
                'meta_keywords' => 'terms, conditions, agreement, usage policy',
                'is_active' => true,
            ]
        );

        // About Us
        StaticPage::firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'content' => <<<'HTML'
<h2>About Us</h2>
<p>Welcome to our platform. We are dedicated to providing exceptional service and innovative solutions to our users.</p>

<h3>Our Mission</h3>
<p>Our mission is to create a platform that empowers individuals and businesses to achieve their goals through cutting-edge technology and exceptional customer service.</p>

<h3>Our Values</h3>
<ul>
<li><strong>Integrity:</strong> We conduct our business with honesty and transparency.</li>
<li><strong>Innovation:</strong> We continuously improve our platform and services.</li>
<li><strong>Customer Focus:</strong> Your satisfaction is our top priority.</li>
<li><strong>Excellence:</strong> We strive for the highest quality in everything we do.</li>
</ul>

<h3>Our Story</h3>
<p>Since our founding, we have been committed to delivering value to our users. Our team of experienced professionals works tirelessly to ensure that you receive the best possible experience.</p>

<h3>Our Team</h3>
<p>Our diverse and talented team brings together expertise from various fields to create a platform that meets your needs. We believe in collaboration, creativity, and continuous learning.</p>

<h3>Contact Us</h3>
<p>Have questions? We'd love to hear from you. Reach out to us at support@example.com or visit our contact page.</p>
HTML,
                'meta_description' => 'Learn more about our company, mission, and values.',
                'meta_keywords' => 'about, company, team, mission, values',
                'is_active' => true,
            ]
        );

        // Contact Us
        StaticPage::firstOrCreate(
            ['slug' => 'contact-us'],
            [
                'title' => 'Contact Us',
                'content' => <<<'HTML'
<h2>Contact Us</h2>
<p>We'd love to hear from you! Whether you have a question, feedback, or need assistance, please don't hesitate to reach out.</p>

<h3>Get in Touch</h3>
<p>You can contact us through the following channels:</p>

<h3>Email</h3>
<p><strong>Support:</strong> support@example.com<br/>
<strong>Sales:</strong> sales@example.com<br/>
<strong>General Inquiries:</strong> info@example.com</p>

<h3>Phone</h3>
<p>Call us at: <strong>1-800-EXAMPLE</strong> (Mon-Fri, 9 AM - 5 PM EST)</p>

<h3>Office Hours</h3>
<p>Monday - Friday: 9:00 AM - 5:00 PM EST<br/>
Saturday - Sunday: Closed</p>

<h3>Response Time</h3>
<p>We typically respond to inquiries within 24 business hours. Thank you for your patience!</p>

<h3>Send us a Message</h3>
<p>You can also use the contact form on our platform to send us a message directly. We'll get back to you as soon as possible.</p>
HTML,
                'meta_description' => 'Get in touch with us. Contact information and support channels.',
                'meta_keywords' => 'contact, support, email, phone, help',
                'is_active' => true,
            ]
        );
    }
}
