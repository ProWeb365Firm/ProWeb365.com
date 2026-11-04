ProWeb365 - static HTML version
===============================

Upload the CONTENTS of this folder (including the hidden .htaccess file) to the web root of
proweb365.com, or to any subfolder for a staging preview. All links are relative.

Pages
-----
All 167 page URLs are unchanged (home, services and service pages, city and industry landing
pages, case studies, the blog with all its posts and category/paged archives, contact, etc.).
Each URL is a folder containing index.html.
The 20 old URLs that redirect on the live site (old HVAC / senior-living / city landing URLs,
moved blog posts, /wordpress-web-design/ etc.) still redirect, through small redirect pages.

Contact forms
-------------
Both form types now post to send.php (needs PHP):
- Contact Form 7 forms (19 pages, including both forms on the home page)
- Elementor forms on the industry/city landing pages and /contact-us/
Messages go to info@proweb365.com; change $to in send.php to use another address.
Visitors see the usual "Thank you for your message" after sending. A hidden honeypot field adds
basic spam protection. The Cloudflare Turnstile box on the Contact Form 7 forms only works on
proweb365.com itself, so on a staging domain it may show an error; forms still send either way.

Noindex (staging)
-----------------
Every page has <meta name="robots" content="noindex, nofollow" /> and .htaccess sends an
X-Robots-Tag: noindex, nofollow header, so search engines will not index this copy.
BEFORE GOING LIVE on the real domain: remove that meta line from each page and delete the
"BEGIN noindex ... END noindex" block from .htaccess, or the site will drop out of Google.
