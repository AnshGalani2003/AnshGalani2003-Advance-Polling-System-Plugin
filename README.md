# Advance Polling System

**Contributors:** [anshgalani003](https://profiles.wordpress.org/anshgalani003/)  
**Tags:** poll, survey, voting, analytics, quiz  
**Requires at least:** WordPress 5.8  
**Tested up to:** WordPress 6.8  
**Requires PHP:** 7.4  
**Stable tag:** 1.0  
**License:** GPLv3 or later  
**License URI:** [https://www.gnu.org/licenses/gpl-3.0.en.html](https://www.gnu.org/licenses/gpl-3.0.en.html)

A comprehensive polling system with IP tracking, customizable results display, beautiful UI, and advanced analytics.

---

## Description

**Advance Polling System** is a powerful and feature-rich WordPress plugin that allows you to create beautiful, interactive polls with real-time results, multiple verification methods, and detailed analytics.

Perfect for bloggers, content creators, marketers, and businesses who want to engage their audience and gather valuable feedback.

---

## Key Features

- **Beautiful Modern UI** – Sleek dark-themed design with smooth animations
- **Dual Verification Methods**
  - Cookie-based (Browser tracking)
  - IP Address-based (Device/Network tracking)
- **Customizable Results Display**
  - Show Top 3, Top 5, Top 10, or All results
  - Control what users see after voting
- **Real-time Voting Results** – Live vote counting with beautiful progress bars
- **Advanced Analytics Dashboard**
  - Bar charts and Pie charts
  - Detailed vote breakdown
  - Export-ready statistics
- **Security-First Architecture**
  - Nonce verification on all forms
  - SQL injection prevention
  - XSS protection
  - CSRF protection
- **Fully Responsive Design**
- **Fast & Lightweight**
- **Translation Ready** – Full i18n support
- **Accessibility Compliant** – WCAG 2.1 standards
- **Easy Shortcode Integration** – `[aps_poll id="X"]`

---

## Verification Methods Explained

### Cookie Verification (Browser-based)
- Tracks votes per browser session
- Users can vote again from different browsers
- Best for: Public polls, general audience engagement
- Privacy-friendly with no personal data storage

### IP Address Verification (Device-based)
- Tracks votes per device/network
- More secure, prevents multiple votes from the same device
- Best for: Important surveys, contest voting, serious polls
- Note: Multiple users on the same network share the same IP

---

## Results Display Options

Control how many results are shown to voters:

- **Top 3 Results** – Shows only the 3 most popular choices (default)  
- **Top 5 Results** – Displays top 5 options  
- **Top 10 Results** – Shows top 10 choices  
- **All Results** – Displays complete poll results  

Choose per poll what makes sense for your use case!

---

## Use Cases

- Customer feedback surveys
- Product preference polls
- Event planning decisions
- Content topic voting
- Market research
- Political opinion polls
- Sports predictions
- Community engagement
- Website feedback
- Contest voting

---

## Admin Features

- Intuitive poll creation interface
- Real-time vote tracking
- Edit polls without losing votes
- Delete polls with one click
- Visual analytics dashboard
- Bulk actions support
- Search and filter polls

---

## Frontend Features

- Auto-hide success message (5 seconds)
- Smooth progress bar animations
- Gradient colour schemes
- Top results highlighting
- Responsive mobile design
- Dark theme by default
- Custom CSS support

---

## Installation

### Automatic Installation

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Advance Polling System"
4. Click "Install Now" and then "Activate"
5. Go to Polls > Add New to create your first poll

### Manual Installation

1. Download the plugin ZIP file
2. Upload to `/wp-content/plugins/advance-polling-system`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to Polls > Add New to create your first poll

### After Activation

1. Click on "Polls" in the WordPress admin menu
2. Click "Add New" to create your first poll
3. Enter your poll question and options
4. Choose verification method (Cookie or IP Address)
5. Select results display (Top 3, Top 5, Top 10, or All)
6. Copy the generated shortcode
7. Paste it in any post, page, or widget: `[aps_poll id="x"]`

---

## Frequently Asked Questions

**Q: Can users vote multiple times?**  
A: No. Depends on your chosen verification method:  
- **Cookie:** Once per browser  
- **IP Address:** Once per device/network  

**Q: What's the difference between Cookie and IP verification?**  
- **Cookie Verification:** Stores a small cookie in user's browser; more privacy-friendly; best for casual polls.  
- **IP Address Verification:** Stores IP in database; more secure; may affect shared networks; best for important surveys.

**Q: How do I change from Top 3 to All results?**  
1. Go to Polls > All Polls  
2. Click "Edit" on your poll  
3. Change "Results Display" dropdown to "All Results"  
4. Click "Update Poll"

**Q: Does it work with caching plugins?**  
Yes, compatible with WP Super Cache, W3 Total Cache, WP Rocket, and similar plugins.

**Q: Is it GDPR compliant?**  
Yes. Only first-party cookies or IP addresses are stored. No external tracking. Users can clear cookies anytime.

**Q: Can I customise the design?**  
Yes! Override the plugin's CSS in your theme's `style.css`.

**Q: What data does this plugin collect?**  
- Cookie Verification: first-party cookie (`aps_poll_voted_X`)  
- IP Address Verification: IP address and User Agent  
- No personal information or third-party tracking

---

## Credits

- Developed by [Ansh Galani](https://anshgalani03.github.io/Personal-Portfolio/)   
- Chart.js library for analytics  
- Icons by WordPress Dashicons

---

## Technical Details

**Database Tables:**  
- `wp_aps_polls` – Stores poll data  
- `wp_aps_poll_options` – Stores poll options  
- `wp_aps_poll_votes` – Stores IP-based votes  

**Requirements:**  
- WordPress 5.8+  
- PHP 7.4+  
- MySQL 5.6+  
- Modern browser with JavaScript enabled  

**Compatible With:**  
- Gutenberg and Classic editor  
- Page builders (Elementor, Divi, etc.)  
- Multisite installations  
- WooCommerce  
- bbPress forums

---

## About the Developer

Advance Polling System is developed and maintained by [Ansh Galani](https://anshgalani03.github.io/Personal-Portfolio/),a WordPress developer passionate about creating user-friendly, secure, and feature-rich plugins.

Thank you for choosing the Advance Polling System! 🎉
