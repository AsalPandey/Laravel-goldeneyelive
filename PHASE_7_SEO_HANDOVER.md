# Phase 7 SEO and Local Search Handover

## Staff publishing checklist

1. Use a unique, plain-language page or article title. Do not repeat “Golden Eye Academy” inside a custom title when the public fallback already includes the brand.
2. Write a concise, factual meta description that matches the visible page. Do not claim rankings, ratings, results, credentials, schedules, availability, or partnerships that have not been verified.
3. Keep the AEO summary short, direct, and consistent with the visible content.
4. Save new articles as drafts first. Preview the draft, confirm its heading, image, links, title, description, and mobile readability, then publish it.
5. After publishing, open the public URL while logged out. Confirm the page loads, the correct title is visible in the browser tab, the featured image works, and all course or inquiry links point to active public pages.
6. Do not paste raw JSON-LD, change robots rules, edit canonical URLs, or create redirects. Those technical controls remain Admin-only.
7. Do not create a second article to replace an existing URL. Update the existing record so established slugs and links remain stable.

## Admin technical checklist

- Set `APP_URL` independently in each environment. Production must use the owner-approved HTTPS canonical domain; staging must use its own hostname and must be protected from indexing in deployment configuration.
- Keep the Laravel `/robots.txt` route authoritative. The obsolete static `public/robots.txt` file was removed because a web server could serve it before Laravel and bypass CMS guardrails.
- Use raw schema only for verified information that is also supported by visible page content. Automatic organization, website, webpage, course, article, and FAQ output should be preferred.
- After an SEO change, inspect the public source for one canonical tag, one appropriate page title and description, parseable JSON-LD, and the intended robots directive.
- Submit the canonical `/sitemap.xml` URL in the verified search-console property after production verification.

## Owner information required

- Confirm the one canonical production hostname, including the `www` or non-`www` choice, and confirm HTTP and alternate-host requests permanently redirect to it.
- Confirm the public academy address, landline/mobile numbers, map destination, and each social-profile URL before adding them to structured data.
- Supply a verified Google Business Profile URL if one exists.
- Supply verified opening hours only if they should be published. None were invented in Phase 7.
- Confirm whether geographic coordinates already stored in the CMS identify the academy entrance accurately.

## Production and Hostinger checks

- Confirm the deployed web root points to Laravel `public/`.
- Confirm `/robots.txt` reaches Laravel after the tracked static file is removed and contains one sitemap directive using the approved canonical host.
- Confirm `/sitemap.xml` is reachable over HTTPS and contains only the approved host.
- Confirm production `APP_URL`, trusted-host handling, HTTPS redirection, and staging noindex protection in the actual server configuration.
- Clear application caches after deployment through the approved deployment process; do not run seeders.
